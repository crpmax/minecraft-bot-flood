## PHP Minecraft bot flood stress tester

### Features
* Connect a lot of bots very fast
* [WIP] Use proxies
* [WIP] Other settings

This script is used for stress testing Minecraft servers,
it can connect a lot of bots very fast.
These bots will not respond to any server packets,
so they will time out after 30 seconds by default.

### Usage
`php minecraft-bot-flood.php <ip> <port> <count>`

The script should support all major versions of Minecraft.
The default protocol version is 754 - for MC 1.16.4,
you can change the `$proto` variable to your needs.
You can find the protocol version list [here](https://wiki.vg/Protocol_version_numbers).

### Some showcase

![Command line usage](https://up.frantajaros.cz/62t1qsxmn1.gif "Command line usage")

![Connected bots](https://up.frantajaros.cz/2uqotmk05l "Connected bots")
