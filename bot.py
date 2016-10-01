import requests
import socket
import urllib
import schedule

from HTMLParser import HTMLParser

# ------  SETTINGS  -------------
network = 'chat.freenode.net'
welcometext = 'Welcome to the freenode Internet Relay Chat Network'
port = 6667
homechan = '##hackenkunjeleren'
controlchar = '$'
nick = 'HKJL'
bindip = '149.210.200.25'
backendurl = 'https://www.hackenkunjeleren.nl/bot.php'
consoleoutput = 1
senderrorstoirc = 0
# -------------------------------

# Set up socket
irc = socket.socket ( socket.AF_INET, socket.SOCK_STREAM )
irc.bind(( bindip, 0 ))

# Connect to network and set NICK and USER
irc.connect ( ( network, port ) )
irc.send ( 'NICK ' + nick + '\r\n' )
irc.send ( 'USER ' + nick + ' ' + nick + ' ' + nick + ' :' + nick + '\r\n' )

h = HTMLParser()

def Send(msg):
    irc.send('PRIVMSG ' + homechan + ' :' + msg +  '\r\n')

def everyMinute():
    r = requests.get( backendurl + '?action=minutecron' )
    Send(h.unescape(r.text.encode('utf-8').strip(' \n\t\r')))

schedule.every(1).minutes.do(everyMinute)

# Main loop
while True:
    action = 'none'
    joined = 0
    data = irc.recv ( 4096 ) 

    # Run our defined periodical jobs
    schedule.run_pending()

    if consoleoutput:
        print data

    if data.find ( welcometext ) != -1 and not joined:
            irc.send ( 'JOIN ' + homechan + '\r\n' )
            joined = 1

    if data.find ( 'PING' ) != -1:
            irc.send ( 'PONG ' + data.split() [ 1 ] + '\r\n' )

    if data.find(homechan) != -1:
        message = data.split(homechan)[0]
        action = message.split(' ')[1]
        givendata = data.split(homechan)[1][2:]

    if action == 'PRIVMSG':
        if givendata.find(controlchar) == 0:
            try: 
                info = givendata[1:].split(None, 1)
                if len(info) == 1:
                    info.append("")
                r = requests.get( backendurl + '?action=' + info[0] + '&args=' + urllib.quote_plus(info[1]))
                Send(h.unescape(r.text.encode('utf-8').strip(' \n\t\r')))
            except Exception, e:
                print e
                if senderrorstoirc:
                    Send(e.encode('utf-8').strip(' \n\t\r'))
        if givendata.find('http') == 0:
            try:
                r = requests.get( backendurl + '?action=gettitle&args=' + urllib.quote_plus(givendata) )
                Send(h.unescape(r.text.encode('utf-8').strip(' \n\t\r')))
            except Exception, e:
                print e
                if senderrorstoirc:
                    Send(e.encode('utf-8').strip(' \n\t\r'))
