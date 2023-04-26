#!/usr/bin/python3
from asyncio.trsock import _RetAddress
from typing import Literal
import mininet.log
import socket
from mininet.node import RemoteController
from mininet.cli import CLI
from mininet.net import Mininet

net = Mininet()


def get_local_ip() -> _RetAddress | Literal["127.0.0.1"]:
    """get_local_ip
    returns a single IP which is the primary (the one with a default route).
    """
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    s.settimeout(0)
    try:
        s.connect(("8.8.8.8", 1))
        IP = s.getsockname()[0]
    except Exception:
        IP = "127.0.0.1"
    finally:
        s.close()
    return IP


def create_topology():
    """create_topology
    Create a Mininet network topology this includes an controller two hosts and a switch
    as for the controller uses OpenFlow13 protocol with the host ipaddr
    """
    ip_addr = get_local_ip()

    net.addController(
        "c0",
        controller=RemoteController,
        ip=ip_addr.__str__(),
        port=6633,
        protocols="OpenFlow13",
    )

    h1 = net.addHost("h1")
    h2 = net.addHost("h2")

    s1 = net.addSwitch("s1")

    net.addLink(h1, s1)
    net.addLink(h2, s1)
    mininet.log.info("topology created with 2 hosts and 1 switch")


if __name__ == "__main__":
    mininet.log.setLogLevel("debug")
    create_topology()
    net.start()
    CLI(net)
    net.stop()
