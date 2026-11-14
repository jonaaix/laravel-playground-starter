#!/usr/bin/env bash
input=$(cat)
session=$(jq -r '.session_id' <<<"$input")
active=$(jq -r '.stop_hook_active // false' <<<"$input")
dir=$(jq -r '.cwd // empty' <<<"$input")
state="${TMPDIR:-/tmp}/claude-stop-checks-$session"

fingerprint() {
  cd "${dir:-$CLAUDE_PROJECT_DIR}" 2>/dev/null || return
  {
    git rev-parse HEAD
    git status --porcelain=v1
    git diff HEAD --binary
    git ls-files -o --exclude-standard -z | xargs -0 shasum
  } 2>/dev/null | shasum | cut -d' ' -f1
}

current=$(fingerprint)
if [ "$active" = true ]; then
  printf '%s' "$current" > "$state"
  exit 0
fi
if [ -f "$state" ] && [ "$(cat "$state")" = "$current" ]; then
  exit 0
fi
jq -n '{decision:"block",reason:"Check your latest code changes against CLAUDE.md and for security vulnerabilities, common ones and any your changes introduced. Fix what you find. Keep planning current. Then commit your own uncommitted work, docs and artifacts included; leave files of other agents alone. Docs and planning files are not code. No code changes → reply only: Checks: no changes ✓. Otherwise → Checks passed ✓."}'
