#!/usr/bin/env python3
"""
Generate a PNG thumbnail from an STL file for Archie.

Simplifies with PyMeshLab, then renders via stl2pov + POV-Ray.
Writes the simplified mesh to /tmp first so POV-Ray avoids path spaces.

Usage:
  stl-thumb.py input.stl output.png
  stl-thumb.py input.stl output.png --max-faces 10000

Requires venv: pymeshlab
Requires system: stl2pov, povray (paths below or STL2POV_CMD / POVRAY_CMD env vars)
"""

from __future__ import annotations

import argparse
import os
import re
import shutil
import subprocess
import sys
import tempfile

import pymeshlab as ml
from PIL import Image

STL2POV_CMD = os.environ.get("STL2POV_CMD", "/usr/local/bin/stl2pov")
POVRAY_CMD = os.environ.get("POVRAY_CMD", "/usr/bin/povray")
DEFAULT_MAX_FACES = 10000
THUMB_WIDTH = 120
THUMB_HEIGHT = 120
CAMERA_ZOOM = 0.62
THUMB_ROTATE_CCW = 90
MESH_COLOR = (0.55, 0.55, 0.55)
SIDE_LIGHT = (40, 45, 75)


def simplify_mesh(stl_path: str, max_faces: int) -> str:
    ms = ml.MeshSet()
    ms.load_new_mesh(stl_path)

    face_count = ms.current_mesh().face_number()
    if face_count > max_faces:
        ms.apply_filter(
            "meshing_decimation_quadric_edge_collapse",
            targetfacenum=max_faces,
            preservenormal=True,
        )

    tmp = tempfile.NamedTemporaryFile(suffix=".stl", delete=False, dir="/tmp")
    tmp.close()
    ms.save_current_mesh(tmp.name)
    return tmp.name


def _parse_vec3(text: str) -> list[float]:
    return [float(part.strip()) for part in text.split(",")]


def _format_vec3(values: list[float]) -> str:
    return "<" + ", ".join(str(value) for value in values) + ">"


def adjust_pov(pov_path: str, zoom: float = CAMERA_ZOOM) -> None:
    with open(pov_path, encoding="utf-8") as handle:
        content = handle.read()

    location_match = re.search(r"location\s*(<[^>]+>)", content)
    look_at_match = re.search(r"look_at\s*(<[^>]+>)", content)
    if location_match and look_at_match:
        location = _parse_vec3(location_match.group(1).strip("<> "))
        look_at = _parse_vec3(look_at_match.group(1).strip("<> "))
        new_location = [
            look_at[index] + zoom * (location[index] - look_at[index])
            for index in range(3)
        ]
        content = re.sub(
            r"location\s*<[^>]+>",
            "location " + _format_vec3(new_location),
            content,
            count=1,
        )

    content = re.sub(
        r"((?:pigment\s*\{\s*(?:color\s+)?(?:rgb|rgbt)\s*)<)[^>]+(>)",
        rf"\g<1>{MESH_COLOR[0]}, {MESH_COLOR[1]}, {MESH_COLOR[2]}\g<2>",
        content,
    )

    content = re.sub(
        r"light_source\s*\{[^}]+\}",
        f"light_source {{ {_format_vec3(list(SIDE_LIGHT))} color rgb<1, 1, 1> }}",
        content,
        count=1,
    )

    with open(pov_path, "w", encoding="utf-8") as handle:
        handle.write(content)


def rotate_png(png_path: str, degrees: int) -> None:
    if not degrees:
        return

    img = Image.open(png_path).convert("RGB")
    width, height = img.size
    rotated = img.rotate(
        degrees,
        expand=True,
        resample=Image.Resampling.BICUBIC,
        fillcolor=(255, 255, 255),
    )
    rw, rh = rotated.size
    left = (rw - width) // 2
    top = (rh - height) // 2
    rotated.crop((left, top, left + width, top + height)).save(png_path, "PNG")


def render_pov(stl_path: str, png_path: str) -> None:
    pov_path = stl_path + ".pov"
    tmp_png = stl_path + ".png"

    with open(pov_path, "w") as pov_file:
        subprocess.run(
            [STL2POV_CMD, stl_path],
            stdout=pov_file,
            check=True,
        )

    adjust_pov(pov_path)

    try:
        subprocess.run(
            [
                POVRAY_CMD,
                f"+I{pov_path}",
                f"+O{tmp_png}",
                "-D",
                "+P",
                f"+W{THUMB_WIDTH}",
                f"+H{THUMB_HEIGHT}",
                "+A0.5",
            ],
            check=True,
        )
        rotate_png(tmp_png, THUMB_ROTATE_CCW)
        shutil.copy2(tmp_png, png_path)
    finally:
        if os.path.isfile(pov_path):
            os.unlink(pov_path)
        if os.path.isfile(tmp_png):
            os.unlink(tmp_png)


def main() -> int:
    parser = argparse.ArgumentParser(description="Render an STL thumbnail PNG for Archie")
    parser.add_argument("input_stl", help="Path to input .stl file")
    parser.add_argument("output_png", help="Path to output .png file")
    parser.add_argument(
        "--max-faces",
        type=int,
        default=DEFAULT_MAX_FACES,
        help=f"Simplify meshes above this face count (default: {DEFAULT_MAX_FACES})",
    )
    args = parser.parse_args()

    if not os.path.isfile(args.input_stl):
        print(f"Input file not found: {args.input_stl}", file=sys.stderr)
        return 1

    output_dir = os.path.dirname(os.path.abspath(args.output_png))
    if output_dir and not os.path.isdir(output_dir):
        print(f"Output directory not found: {output_dir}", file=sys.stderr)
        return 1

    simplified_stl = None
    try:
        simplified_stl = simplify_mesh(args.input_stl, args.max_faces)
        render_pov(simplified_stl, args.output_png)
    finally:
        if simplified_stl and os.path.isfile(simplified_stl):
            os.unlink(simplified_stl)

    print(args.output_png)
    return 0


if __name__ == "__main__":
    sys.exit(main())
