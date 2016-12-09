import requests
import socket
import urllib
import schedule
from HTMLParser import HTMLParser

network     = 'chat.freenode.net'
welcometext = 'Welcome to the freenode Internet Relay Chat Network'
port        = 6667
homechan    = '##hackenkunjeleren'
nick        = 'HKJL'
bindip      = '149.210.200.25'
backendurl  = 'https://www.hackenkunjeleren.nl/bot.php'

def Request(query):
    try:
        r = requests.get( backendurl + query )
        r.raise_for_status()
        h = HTMLParser()
        Send(h.unescape(r.text.strip(' \n\t\r')))
    except Exception, e:
        print e

def Send(msg):
    irc.send('PRIVMSG ' + homechan + ' :' + msg.encode('utf-8') +  '\r\n')

def everyMinute():
    Request('?action=minutecron')

schedule.every(1).minutes.do(everyMinute)

irc = socket.socket ( socket.AF_INET, socket.SOCK_STREAM )
irc.bind(( bindip, 0 ))
irc.connect ( ( network, port ) )
irc.send ( 'NICK ' + nick + '\r\n' )
irc.send ( 'USER ' + nick + ' ' + nick + ' ' + nick + ' :' + nick + '\r\n' )

while True:
    action = 'none'
    joined = 0
    data = irc.recv ( 4096 ) 
    print data
    schedule.run_pending()
    if data.find ( welcometext ) != -1 and not joined:
        irc.send ( 'JOIN ' + homechan + '\r\n' )
        joined = 1
    if data.find ( 'PING' ) != -1:
        irc.send ( 'PONG ' + data.split()[1] + '\r\n' )
    if data.find ( homechan ) != -1:
        Request('?data=' + urllib.quote_plus(data));
