#!/usr/bin/env bash
set -euo pipefail

project_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
version="${1:-$(awk '/^Version: / { print $2; exit }' "$project_dir/theme/style.css")}"
pending=0

printf '%s\n' 'Docszine local validation'
printf '%s\n' '========================'

required_files=(
  "theme/style.css"
  "theme/index.php"
  "theme/functions.php"
  "theme/readme.txt"
  "theme/LICENSE"
  "theme/screenshot.png"
  "theme/languages/docszine.pot"
)

for relative_path in "${required_files[@]}"; do
  if [ ! -f "$project_dir/$relative_path" ]; then
    printf 'FAIL missing: %s\n' "$relative_path"
    exit 1
  fi
done
printf '%s\n' 'Required theme files: PASS'

find "$project_dir/theme" "$project_dir/plugin" "$project_dir/tests" -type f -name '*.php' -print0 | xargs -0 -n1 php -l >/dev/null
printf '%s\n' 'Theme, plugin, and test PHP syntax: PASS'

if command -v node >/dev/null 2>&1; then
  find "$project_dir/theme" "$project_dir/plugin" -type f -name '*.js' -print0 | xargs -0 -n1 node --check
  printf '%s\n' 'JavaScript syntax: PASS'
else
  printf '%s\n' 'JavaScript syntax: PENDING (node is not installed)'
  pending=1
fi

php "$project_dir/tests/test-docs-tree.php"
php "$project_dir/tests/test-heading-ids.php"
php "$project_dir/tests/test-theme-contract.php"
php "$project_dir/tests/test-content-contract.php"
php "$project_dir/tests/test-readme-contract.php"
printf '%s\n' 'Dependency-free regression tests: PASS'

if command -v wp >/dev/null 2>&1; then
  wp i18n make-pot "$project_dir/theme" "$project_dir/theme/languages/docszine.pot" --slug=docszine --domain=docszine --skip-js --quiet --headers='{"POT-Creation-Date":"2000-01-01T00:00:00+00:00"}' >/dev/null
  printf '%s\n' 'Theme POT generation: PASS'
else
  printf '%s\n' 'Theme POT generation: PENDING (wp-cli is not installed)'
  pending=1
fi

phpcs_bin=''
if command -v phpcs >/dev/null 2>&1; then
  phpcs_bin="$(command -v phpcs)"
elif [ -x "$project_dir/vendor/bin/phpcs" ]; then
  phpcs_bin="$project_dir/vendor/bin/phpcs"
fi

if [ -n "$phpcs_bin" ]; then
  if "$phpcs_bin" -i | grep -qi 'WordPress'; then
    "$phpcs_bin" --standard="$project_dir/phpcs.xml.dist" "$project_dir/theme"
    printf '%s\n' 'WordPress Coding Standards: PASS'
  else
    printf '%s\n' 'WordPress Coding Standards: PENDING (WordPress standard is not installed)'
    pending=1
  fi
else
  printf '%s\n' 'WordPress Coding Standards: PENDING (phpcs is not installed)'
  pending=1
fi

stylelint_bin=''
if command -v stylelint >/dev/null 2>&1; then
  stylelint_bin="$(command -v stylelint)"
elif [ -x "$project_dir/node_modules/.bin/stylelint" ]; then
  stylelint_bin="$project_dir/node_modules/.bin/stylelint"
fi

if [ -n "$stylelint_bin" ]; then
  "$stylelint_bin" "$project_dir/theme/assets/css/*.css"
  printf '%s\n' 'Stylelint: PASS'
else
  printf '%s\n' 'Stylelint: PENDING (stylelint is not installed)'
  pending=1
fi

line_ending_file=''
while IFS= read -r text_file; do
  if LC_ALL=C grep -q $'\r' "$text_file"; then
    line_ending_file="$text_file"
    break
  fi
done < <(find "$project_dir/theme" -type f \( -name '*.php' -o -name '*.css' -o -name '*.js' -o -name '*.txt' -o -name '*.md' -o -name '*.svg' -o -name '*.pot' \) -print)
if [ -n "$line_ending_file" ]; then
  printf 'FAIL mixed/CRLF line ending in: %s\n' "$line_ending_file"
  exit 1
fi
printf '%s\n' 'Text line endings: PASS'

if command -v theme-check >/dev/null 2>&1; then
  theme-check "$project_dir/theme"
  printf '%s\n' 'Theme Check: PASS'
elif [ -n "${DOCSZINE_QA_PATH:-}" ] && command -v studio >/dev/null 2>&1; then
  diff -qr "$project_dir/theme" "$DOCSZINE_QA_PATH/wp-content/themes/docszine"
  studio wp --path "$DOCSZINE_QA_PATH" theme-check run docszine
  printf '%s\n' 'Native Studio Theme Check: PASS'
else
  printf '%s\n' 'Theme Check: PENDING (official Theme Check runner is not installed)'
  pending=1
fi

bash "$project_dir/scripts/package-release.sh" "$version" 0.1.1 >/dev/null
theme_zip="$project_dir/build/docszine-theme-$version.zip"
prohibited_entry=''
while IFS= read -r package_entry; do
  package_base="${package_entry##*/}"
  case "$package_base" in
    .[!.]*|Thumbs.db|desktop.ini|project.properties|project.xml|*.kpf|php.ini|dwsync.xml|error_log|web.config|*.sql|__MACOSX|*.zip|*.sh)
      prohibited_entry="$package_entry"
      break
      ;;
    wpml-config.xml|loco.xml|phpcs.xml)
      ;;
    *.xml)
      prohibited_entry="$package_entry"
      break
      ;;
  esac
done < <(zipinfo -1 "$theme_zip")
if [ -n "$prohibited_entry" ]; then
  printf 'FAIL package contains prohibited file: %s\n' "$prohibited_entry"
  exit 1
fi
unzip -t "$theme_zip" >/dev/null
printf '%s\n' 'Theme package structure: PASS'

if [ "$pending" -ne 0 ]; then
  printf '%s\n' 'Validation result: INCOMPLETE (required tools are still pending)'
  exit 2
fi

printf '%s\n' 'Validation result: PASS'
