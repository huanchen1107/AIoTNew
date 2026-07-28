#!/usr/bin/env bash

# startup.sh - Environment Check, Pull latest, Read Docs, and Show To-Dos

echo "=================================================="
echo "⚡ Starting Today's Development Session... ⚡"
echo "=================================================="

# --------------------------------------------------
# 0. Pre-checks: grill-me skill and openspec CLI
# --------------------------------------------------
echo "🔍 Performing pre-checks..."

# Check grill-me skill
GRILL_ME_INSTALLED=false
GRILL_ME_PATH=""

# Search in Workspace and Global custom roots
PATHS_TO_CHECK=(
  ".agents/skills/grill-me/SKILL.md"
  "$HOME/.gemini/config/skills/grill-me/SKILL.md"
  "/c/Users/admin/.gemini/config/skills/grill-me/SKILL.md"
)

for p in "${PATHS_TO_CHECK[@]}"; do
  if [ -f "$p" ]; then
    GRILL_ME_INSTALLED=true
    GRILL_ME_PATH="$p"
    break
  fi
done

if [ "$GRILL_ME_INSTALLED" = true ]; then
  echo -e "  [OK] grill-me skill is installed at: $GRILL_ME_PATH"
else
  echo -e "  [WARNING] grill-me skill is NOT installed."
  echo "    To install, clone or copy the skill folder from:"
  echo "    https://github.com/mattpocock/skills/tree/main/skills/productivity/grill-me"
  echo "    into ~/.gemini/config/skills/grill-me or .agents/skills/grill-me"
fi

# Check openspec CLI
if command -v openspec &>/dev/null; then
  echo -e "  [OK] openspec CLI is installed (version $(openspec --version 2>/dev/null || echo "unknown"))."
else
  echo -e "  [WARNING] openspec CLI is NOT installed."
  echo "    To install, run: npm install -g @fission-ai/openspec@latest"
fi

echo ""

# --------------------------------------------------
# 1. Pull latest from remote
# --------------------------------------------------
echo "📥 Syncing codebase with GitHub..."
git pull origin main || git pull origin master || echo "⚠️  Could not pull latest changes from remote. Proceeding anyway."
echo ""

# --------------------------------------------------
# 2. Read README.md, logs, and handover.md
# --------------------------------------------------
echo "📖 Reading project documents..."
echo "--------------------------------------------------"

if [ -f "README.md" ]; then
  echo "📄 [README.md]"
  cat README.md
  echo "--------------------------------------------------"
else
  echo "⚠️  README.md not found."
fi

# Read logs (either logs.md or files under logs/ directory)
if [ -f "logs.md" ]; then
  echo "📄 [logs.md]"
  cat logs.md
  echo "--------------------------------------------------"
elif [ -d "logs" ]; then
  echo "📁 [logs/ folder]"
  for f in logs/*; do
    if [ -f "$f" ]; then
      echo "--- File: $f ---"
      cat "$f"
    fi
  done
  echo "--------------------------------------------------"
else
  echo "ℹ️  No logs.md or logs/ directory found."
fi

if [ -f "handover.md" ]; then
  echo "📄 [handover.md]"
  cat handover.md
  echo "--------------------------------------------------"
else
  echo "ℹ️  handover.md not found."
fi

# --------------------------------------------------
# 3. Output today's To-Do list
# --------------------------------------------------
echo "📝 Today's To-Do List:"
if [ -f "tasks.md" ]; then
  cat tasks.md
elif [ -f "task.md" ]; then
  cat task.md
else
  echo "ℹ️  No tasks.md found. Creating a template tasks.md for you."
  cat << 'EOF' > tasks.md
# Tasks

- [ ] Initialize project configuration
- [ ] Task 1
- [ ] Task 2
EOF
  cat tasks.md
fi
echo "=================================================="
