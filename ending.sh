#!/usr/bin/env bash

# ending.sh - Verify tasks, Update documentation/logs, and Commit/Push

echo "=================================================="
echo "🏁 Wrapping up Today's Development Session... 🏁"
echo "=================================================="

# --------------------------------------------------
# 1. Verify tasks in tasks.md
# --------------------------------------------------
TASK_FILE=""
if [ -f "tasks.md" ]; then
  TASK_FILE="tasks.md"
elif [ -f "task.md" ]; then
  TASK_FILE="task.md"
fi

if [ -n "$TASK_FILE" ]; then
  echo "📊 Checking tasks in $TASK_FILE..."
  REMAINING=$(grep -c "-\s*\[ \]" "$TASK_FILE" || true)
  COMPLETED=$(grep -c "-\s*\[[xX]\]" "$TASK_FILE" || true)
  TOTAL=$((REMAINING + COMPLETED))
  
  echo "  Progress: $COMPLETED completed, $REMAINING remaining out of $TOTAL total."
  if [ "$REMAINING" -gt 0 ]; then
    echo "  ⚠️  Pending tasks remaining:"
    grep "-\s*\[ \]" "$TASK_FILE"
  else
    echo "  🎉 Fantastic! All tasks are fully completed."
  fi
  echo ""
  
  # Ask if user wants to update tasks.md before finalizing
  read -p "📝 Do you want to edit $TASK_FILE now? (y/N): " edit_tasks
  if [[ "$edit_tasks" =~ ^[yY] ]]; then
    if command -v code &>/dev/null; then
      code "$TASK_FILE"
      read -p "   Press Enter after you save and close $TASK_FILE to continue..."
    elif command -v notepad &>/dev/null; then
      notepad "$TASK_FILE"
      read -p "   Press Enter after you save and close $TASK_FILE to continue..."
    else
      echo "   Please edit $TASK_FILE manually in your editor."
      read -p "   Press Enter when you are done to continue..."
    fi
    # Recheck remaining/completed tasks
    REMAINING=$(grep -c "-\s*\[ \]" "$TASK_FILE" || true)
    COMPLETED=$(grep -c "-\s*\[[xX]\]" "$TASK_FILE" || true)
    TOTAL=$((REMAINING + COMPLETED))
    echo "  Updated Progress: $COMPLETED completed, $REMAINING remaining out of $TOTAL total."
  fi
else
  echo "⚠️  No task file (tasks.md or task.md) found to verify."
fi
echo ""

# --------------------------------------------------
# 2. Write log entry
# --------------------------------------------------
echo "📝 Log Entry Creation:"
read -p "💬 Enter a log entry summarizing today's accomplishments: " log_entry
if [ -n "$log_entry" ]; then
  TIMESTAMP=$(date "+%Y-%m-%d %H:%M:%S")
  if [ ! -f "logs.md" ]; then
    echo "# Project Logs" > logs.md
  fi
  echo -e "\n## [$TIMESTAMP]\n- $log_entry" >> logs.md
  echo "✅ Log entry added to logs.md."
else
  echo "ℹ️  No log entry provided. Skipping log update."
fi
echo ""

# --------------------------------------------------
# 3. Update README.md with timestamp
# --------------------------------------------------
if [ -f "README.md" ]; then
  echo "⏰ Updating README.md timestamp..."
  CURRENT_TIME=$(date "+%Y-%m-%d %H:%M:%S")
  
  # Robust timestamp injection using Python if available, otherwise fallback to sed
  if command -v python3 &>/dev/null; then
    python3 -c "import re; f=open('README.md','r+'); c=f.read(); f.seek(0); f.write(re.sub(r'Last Updated:.*', 'Last Updated: $CURRENT_TIME', c) if 'Last Updated:' in c else c + '\n\nLast Updated: $CURRENT_TIME\n'); f.truncate()"
  elif command -v python &>/dev/null; then
    python -c "import re; f=open('README.md','r+'); c=f.read(); f.seek(0); f.write(re.sub(r'Last Updated:.*', 'Last Updated: $CURRENT_TIME', c) if 'Last Updated:' in c else c + '\n\nLast Updated: $CURRENT_TIME\n'); f.truncate()"
  else
    if grep -q "Last Updated:" README.md; then
      sed -i "s/Last Updated:.*/Last Updated: $CURRENT_TIME/g" README.md
    else
      echo -e "\nLast Updated: $CURRENT_TIME" >> README.md
    fi
  fi
  echo "✅ README.md timestamp updated to: $CURRENT_TIME"
else
  echo "⚠️  README.md not found. Skipping timestamp update."
fi
echo ""

# --------------------------------------------------
# 4. Git commit and push everything to GitHub
# --------------------------------------------------
echo "🚀 Preparing to push to GitHub..."
git add -A

echo "📂 Staged files:"
git status -s

echo ""
read -p "💬 Enter commit message (leave blank for default): " commit_msg
if [ -z "$commit_msg" ]; then
  commit_msg="Update tasks, logs, and README [$(date '+%Y-%m-%d %H:%M:%S')]"
fi

git commit -m "$commit_msg"

echo "📤 Pushing to GitHub..."
CURRENT_BRANCH=$(git branch --show-current)
git push -u origin "$CURRENT_BRANCH"

echo "=================================================="
echo "✨ Session completed successfully! ✨"
echo "=================================================="
