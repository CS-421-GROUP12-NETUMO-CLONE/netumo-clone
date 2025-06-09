#!/bin/bash

IMAGES=("app1" "app2" "worker" "scheduler")

for NAME in "${IMAGES[@]}"; do
  echo "🔄 Tagging and pushing $NAME..."
  docker tag netumo-clone_${NAME} edibily12/netumo_${NAME}:latest
  docker push edibily12/netumo_${NAME}:latest
done
