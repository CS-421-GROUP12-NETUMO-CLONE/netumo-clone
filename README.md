Tag
docker tag netumo-clone-app1 edibily12/netumo_app1:latest
docker tag netumo-clone-app2 edibily12/netumo_app2:latest
docker tag netumo-clone-worker edibily12/netumo_worker:latest
docker tag netumo-clone-scheduler edibily12/netumo_scheduler:latest

Push
docker push edibily12/netumo_app1:latest
docker push edibily12/netumo_app2:latest
docker push edibily12/netumo_worker:latest
docker push edibily12/netumo_scheduler:latest
