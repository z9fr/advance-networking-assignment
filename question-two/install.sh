#!/bin/bash

# refer to the below blog for more details related to opendaylight
# https://www.brianlinkletter.com/2016/02/using-the-opendaylight-sdn-controller-with-the-mininet-network-emulator/

# install curl for download data
sudo apt install curl


# apperely to make opendaylight work we need java but the issue is
# to set this up it has a dependacy for xml and its apperely removed from jdk 11 
# and we need to setup jdk 8 for this
apt install openjdk-8-jdk

# install mininet
sudo apt install mininet

# download opendaylight release
curl -XGET -O https://nexus.opendaylight.org/content/repositories/opendaylight.release/org/opendaylight/integration/karaf/0.8.4/karaf-0.8.4.zip

# extract the installer
unizp karaf-0.8.4.zip

# after unzip it will create an folder called karaf-0.8.4
# to start an instance of this 
# after setting up we should be able to use `./bin/karaf` to get an shell
./bin/karaf
