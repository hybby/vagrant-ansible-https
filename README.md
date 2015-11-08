# vagrant-ansible-https

## overview

this project uses Ansible and Vagrant to provision multiple secure webservers on RHEL/CentOS guests

the Ansible provisioner applies the `site.yml` playbook to all hosts in the `hosts` inventory file.

from this playbook, two roles are called, `common` and `https_webserver`.  the following is configured:

  * `common`
    * installs a few pre-req packages and configures EPEL packaging

  * `https_webserver`
    * installs the `httpd`, `php`, `mod_ssl` and `openssl` are installed
    * configures `iptables` to allow incoming connections
    * sets up a secure directory structure to keep certs, csrs and private keys
    * generates a csr for the server (ensuring that the CN is correct)
    * signs the csr with its own private key (self-signed)
    * configures apache to serve a single `.php` page out of its DocumentRoot
    * finally, restarts `httpd` and `iptables`


where possible, all major configurable items (paths, ports, etc) have been parameterised.

you should be able to override them with the Ansible provisioner's `extra_vars` function.
  
there's an example of how to do this in the included Vagrantfile (see below).



## pre-reqs

make sure you've got [Vagrant](http://vagrantup.com) installed 

make sure you've got [Ansible](http://ansible.com) installed

if you're on OSX and you don't have Ansible installed, simply issue:

    pip install ansible

if you don't have `pip` installed, check out [the docs](http://pip.readthedocs.org/en/stable/installing/)

if you want more info about the osx install process for ansible, check out [the docs](http://docs.ansible.com/ansible/intro_installation.html#latest-releases-via-pip)

also, since the hosts attempt to install the `epel-release` rpm from the internet, you'll need internet access


## installation

clone this repository as so:

    git clone git@github.com:hybby/vagrant-ansible-https.git

i've set the base box in the `Vagrantfile` to:

    config.vm.box = "spantree/Centos-6.5_x86-64"

if you don't have this box, install it with:

    vagrant box add spantree/Centos-6.5_x86-64


## using this project

simply run:

    vagrant up

this will provision two servers, `webserver01` and `webserver02`

when provisioning is complete, you should be provided the relevant URLs to pop into a browser.  

from there, you should see a webpage which tests a couple of things and reports back to you.

if you don't have a browser, you could use `curl https://localhost:$port -k`

**note:** the certificate installed on each host will flag up as untrusted on your system due to it being self-signed.

i really wanted to try out [Let's Encrypt!](https://www.letsencrypt.org), but it seems that it's a closed beta at the moment :(


## expected results

a webpage that looks like the following should be served out from each host:

    hello from webserver0x!

     _________________________________________ 
    / if you can see this, your secure apache \
    \ webserver was installed successfully!   /
     ----------------------------------------- 
            \   ^__^
             \  (oo)\_______
                (__)\       )\/\
                    ||----w |
                    ||     ||

    https seems to be working!

    php seems to be working!

    looks like tls is enabled, too! nice one.

    looks like port 80 on the server is closed! great!


## performance 

four and a half minutes to build and configure two vms

    $ time vagrant up
    ...
    real  4m20.371s
    user  0m7.263s
    sys 0m4.404s

i'd estimate that for every VM added to the environment it'll be another 2m10s.

there's a way to make Vagrant only call Ansible once after servers have been powered on

it involves messing around with Vagrant's SSH keys though!


## networking / ports 

each guest will be configured to have `httpd` listening on `tcp/10443`, secured using `mod_ssl`.  

i've configured vagrant to forward these ports to (hopefully) non-used ports on your local machine

  * `webserver01` will forward to `tcp/8441` on the host machine
  * `webserver02` will forward to `tcp/8442` on the host machine

note that if you change the port that `httpd` listens on in the Vagrantfile, you need to change the forwarding rules too.

regular `http` traffic on `tcp/80` has been disabled.


## overriding settings in vagrant / ansible

as mentioned, you can force vagrant to override settings by altering the `ansible.extra_vars` setting

for example, if you wanted to alter the ServerAdmin directive to include your own email address, you could:

    ansible.extra_vars = { webmaster: "your@email.here" }
    
then, you can reprovision the hosts using

    vagrant provision

following this, the relevant setting should be updated across all of your hosts.


## adding additional hosts

you can do this, super-easy.  have a look in the Vagrantfile for a block like this:

    config.vm.define "webserver01" do |server|
      server.vm.box = "spantree/Centos-6.5_x86-64"
      server.vm.network :private_network, ip: "192.168.10.11"
      server.vm.network "forwarded_port", guest: 10443, host: 8441
      server.vm.hostname = "webserver01.hybby.com"
      server.vm.post_up_message = "hey!  webserver01 is up!  check out https://localhost:8441"
    end

grab this, update the hostnames, ip address and host port and paste it below the last instance

then, make sure to add the host to the `hosts` inventory file.

after this, a `vagrant up` should bring up your new host.


## removing all hosts

you can completely destroy all hosts that vagrant / ansible has built with:

    vagrant destroy
