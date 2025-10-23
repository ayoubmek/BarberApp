#!/bin/bash
set -e

# Run Symfony auto-scripts (cache clear, etc.) at runtime
composer run-script auto-scripts

# Execute the main container command (Apache)
exec "$@"
