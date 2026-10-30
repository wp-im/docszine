#!/usr/bin/env bash
set -euo pipefail

project_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
# Usage: package-release.sh THEME_VERSION PLUGIN_VERSION
theme_version="${1:-0.2.0}"
plugin_version="${2:-0.1.1}"
build_dir="$project_dir/build"
stage_dir="$(mktemp -d "${TMPDIR:-/tmp}/docszine-package.XXXXXX")"

cleanup() {
	rm -rf "$stage_dir"
}
trap cleanup EXIT

mkdir -p "$build_dir"
rm -f "$build_dir/docszine-theme-$theme_version.zip" "$build_dir/docszine-editorial-$plugin_version.zip" "$build_dir/docszine-source-$theme_version.zip"

mkdir -p "$stage_dir/theme/docszine" "$stage_dir/plugin/docszine-editorial"
cp -R "$project_dir/theme/." "$stage_dir/theme/docszine/"
cp -R "$project_dir/plugin/." "$stage_dir/plugin/docszine-editorial/"

# Normalize timestamps and archive metadata so identical source trees produce
# identical ZIPs. The package content, not the local filesystem clock, should
# determine the release checksum.
find "$stage_dir" -exec touch -t 200001010000 {} +

( cd "$stage_dir/theme" && find docszine -type f -print | LC_ALL=C sort | zip -X -q "$build_dir/docszine-theme-$theme_version.zip" -@ )
( cd "$stage_dir/plugin" && find docszine-editorial -type f -print | LC_ALL=C sort | zip -X -q "$build_dir/docszine-editorial-$plugin_version.zip" -@ )

mkdir -p "$stage_dir/source/docszine"
cp -R "$project_dir/theme" "$stage_dir/source/docszine/"
cp -R "$project_dir/plugin" "$stage_dir/source/docszine/"
cp -R "$project_dir/content" "$stage_dir/source/docszine/"
cp -R "$project_dir/tests" "$stage_dir/source/docszine/"
cp -R "$project_dir/scripts" "$stage_dir/source/docszine/"
cp "$project_dir/README.md" "$project_dir/PLAN.md" "$stage_dir/source/docszine/"
find "$stage_dir/source" -exec touch -t 200001010000 {} +
( cd "$stage_dir/source" && find docszine -type f -print | LC_ALL=C sort | zip -X -q "$build_dir/docszine-source-$theme_version.zip" -@ )

for package in "$build_dir/docszine-theme-$theme_version.zip" "$build_dir/docszine-editorial-$plugin_version.zip" "$build_dir/docszine-source-$theme_version.zip"; do
	shasum -a 256 "$package"
	unzip -t "$package" >/dev/null
done
