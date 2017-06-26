#!/bin/bash
set -ex
#### build image
docker build -t fossology/fossology-test . 

#### create network
docker network create --subnet=172.18.0.0/16 fossology-testnet

#### create container and set IP
docker create -p 8081:8081 --name fossology-test --net fossology-testnet --ip 172.18.0.22 fossology/fossology-test

#### start container
docker start fossology-test

#### is container running
docker ps | grep fossology-test

#### fossology needs up to 15 seconds to startup
sleep 15

#### is fossology reachable? --> check title
curl -L -s http://172.18.0.22/repo/ | grep "<title>Getting Started with FOSSology</title>"
