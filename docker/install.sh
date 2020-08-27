apt-get update && apt-get install -y wget tar unzip dnsutils python3-pip nmap dnsrecon whatweb git masscan curl
mkdir -p ~/.ssh && ssh-keyscan github.com >> ~/.ssh/known_hosts


# Crawler dependencies
curl -sL https://deb.nodesource.com/setup_12.x | bash -
npm install apify

cd /opt

# WPScan
#apt-get install -y build-essential libcurl4-openssl-dev libxml2 libxml2-dev libxslt1-dev ruby-dev libgmp-dev zlib1g-dev
#wget https://cache.ruby-lang.org/pub/ruby/2.7/ruby-2.7.1.tar.gz
#tar -xzf ruby-2.7.1.tar.gz
#cd ruby-2.7.1
#./configure
#make
#make install --with-openssl-dir=/usr/bin/openssl
#cd ..
#rm -r ruby-2.7.1 ruby-2.7.1.tar.gz
#gem install wpscan
#wpscan --update

# Install Go
export GOPATH=$HOME/go
export PATH=$PATH:/usr/local/go/bin
wget https://dl.google.com/go/go1.13.3.linux-amd64.tar.gz
tar -C /usr/local -xzf go1.13.3.linux-amd64.tar.gz
rm go1.13.3.linux-amd64.tar.gz

# Subjack
go get github.com/haccer/subjack
cp /root/go/bin/subjack /opt/subjack/subjack
cp /root/go/src/github.com/haccer/subjack/fingerprints.json /opt/subjack/fingerprints.json

# Sonar
apt-get install -y pv jq pigz

# MassDNS
git clone https://github.com/blechschmidt/massdns.git
cd /opt/massdns
make
cd /opt
#wget -O massdns_all.txt https://gist.github.com/jhaddix/f64c97d0863a78454e44c2f7119c2a6a/raw/96f4e51d96b2203f19f6381c8c545b278eaa0837/all.txt

# Amass
wget https://github.com/OWASP/Amass/releases/download/v3.5.5/amass_v3.5.5_linux_arm64.zip
unzip amass_v3.5.5_linux_arm64.zip
mv amass_v3.5.5_linux_arm64 amass
rm amass_v3.5.5_linux_arm64.zip

# TestSSL
git clone https://github.com/drwetter/testssl.sh

# CheckDMARC
git clone https://github.com/domainaware/checkdmarc
pip3 install -U checkdmarc

# Common Crawl
git clone https://github.com/si9int/cc.py.git

# Hydra
apt-get -y install hydra

# OneForAll
apt-get install -y build-essential zlib1g-dev libncurses5-dev libgdbm-dev libnss3-dev libssl-dev libreadline-dev libffi-dev libsqlite3-dev
curl -O https://www.python.org/ftp/python/3.8.2/Python-3.8.2.tar.xz
tar -xf Python-3.8.2.tar.xz
cd /opt/Python-3.8.2
./configure --enable-optimizations --enable-loadable-sqlite-extensions
make -j 4
make altinstall
cd /opt
rm -rf Python-3.8.2
rm -rf Python-3.8.2.tar.xz
git clone https://github.com/shmilylty/OneForAll.git
cd /opt/OneForAll/
python3.8 -m pip install -U pip setuptools wheel
pip3.8 install -r requirements.txt
pip3.8 install uvloop

# PDF / Screenshots dependencies
apt-get install -y libgbm-dev nodejs gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget
npm install --global --unsafe-perm puppeteer
chmod -R o+rx /usr/lib/node_modules/puppeteer/.local-chromium

# TruffleHog
pip install gitdb2==3.0.0 trufflehog