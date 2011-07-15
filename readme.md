# TF2Logs.com - Version 2.0

This is a complete rewrite of the TF2Logs.com website, running Node.JS, and express. This will include uploading logs and receiving logs directly from a TF2 server. There will also be more, additional features.

# Install

In the directory that you wish to hold the project, do the following:

```
$ git clone http://github.com/barncow/tf2logs
$ cd tf2logs
$ npm install -d
$ npm link
$ cp conf/default/*.js conf
```

This should download the project, switch to the project's directory, and then link the project to the global install directory. This gives you access to the `tf2logs` command (more commands will come soon), and will allow you to perform code changes in this directory without having to constantly reinstall or symlink. Next, we copied all of the default javascript configuration to where the server can find it. You will want to review these files, and make changes as necessary.

If you have problems with the `npm link` command, try upgrading npm: `curl http://npmjs.org/install.sh | sh`

# The `tf2logs` Command

The `tf2logs` command allows you to start up the server. By default, with no switches passed, it will launch into development mode (default port 3001). Here are the possible switches:
`-d` development (for running the server locally)
`-t` test (for unit testing)
`-q` QA (for pushing to a server for testing, potentially with other people)
`-p` production (live use)

