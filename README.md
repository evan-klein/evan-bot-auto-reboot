# evan-bot-auto-reboot

Sometimes servers do weird things. Like when a rogue process starts taking up more and more CPU. Or, when a memory leak causes your server to run out of memory.

When that happens, it's often best to just quickly reboot the server.

This PHP script automates that.

It's designed to be run as a cron job every minute. If it detects your server's 5 minute CPU load average is too high or that your server is running out of memory, it'll reboot your server.

If passed one or more email addresses, it'll also send an email notification.

## Installation

```sh
curl https://raw.githubusercontent.com/evan-klein/evan-bot-auto-reboot/master/evan-bot-auto-reboot.php > ~/evan-bot-auto-reboot.php
curl https://raw.githubusercontent.com/evan-klein/ek/master/ek.php > ~/ek.php
```

## Usage

#### Cron entry, automated server reboot, when needed

```
* * * * * php ~/evan-bot-auto-reboot.php | sh
```

#### Cron entry, automated server reboot, when needed, with email notification

```
* * * * * php ~/evan-bot-auto-reboot.php --email_to=user1@example.com | sh
```

#### Cron entry, automated server reboot, when needed, with email notification sent to multiple people

```
* * * * * php ~/evan-bot-auto-reboot.php --email_to=user1@example.com,user2@example.com | sh
```

#### Cron entry, test mode

```
* * * * * php ~/evan-bot-auto-reboot.php --email_to=user1@example.com,user2@example.com --test_mode=true 2>> /var/log/evan-bot-auto-reboot-stderr.log 1>> /var/log/evan-bot-auto-reboot-stdout.log
```

> In test mode, when the script detects a problem, an email notification is sent but the server is _not_ rebooted. `stdout` and `stderr` are logged to the log files specified

## Compatibility

Tested on:
- Ubuntu Server
	- 20.04 LTS
	- 22.04 LTS