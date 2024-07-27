## PHP Minecraft bot flood stress tester
**⚠ Please check out my newer and better app - [mc-bots](https://github.com/crpmax/mc-bots)**

### Features
* Connect a lot of bots very fast
* [WIP] Use proxies
* [WIP] Other settings

This script is used for stress testing Minecraft servers,
it can connect a lot of bots very fast.
These bots will not respond to any server packets,
so they will time out after 30 seconds by default.

### Installation
You need at least PHP version 5.  
Download the script minecraft-bot-flood.php.

### Usage
`php minecraft-bot-flood.php <protoversion> <ip> <port> <count>`

e.g. for MC 1.20.4:  
`php minecraft-bot-flood.php 765 localhost 25565 10`  

The script should support all major versions of Minecraft.
You can find the protocol version list [here](https://wiki.vg/Protocol_version_numbers).

### Some showcase

![Command line usage](https://up.frantajaros.cz/62t1qsxmn1.gif "Command line usage")

![Connected bots](https://up.frantajaros.cz/2uqotmk05l "Connected bots")
