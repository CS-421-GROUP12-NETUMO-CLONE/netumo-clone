Tag
docker tag netumo_app1 edibily12/netumo_app1:latest
docker tag netumo_app2 edibily12/netumo_app2:latest
docker tag netumo_worker edibily12/netumo_worker:latest
docker tag netumo_scheduler edibily12/netumo_scheduler:latest

Push
docker push edibily12/netumo_app1:latest
docker push edibily12/netumo_app2:latest
docker push edibily12/netumo_worker:latest
docker push edibily12/netumo_scheduler:latest
