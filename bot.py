import requests
import socket
import urllib
import schedule
import argparse
from HTMLParser import HTMLParser

parser = argparse.ArgumentParser(
  description='Run the Hackenkunjeleren IRC bot',
  formatter_class=argparse.ArgumentDefaultsHelpFormatter)

parser.add_argument('--network',
                    dest='network',
                    help='IRC server hostname or IP',
                    default='chat.freenode.net')

parser.add_argument('--port',
                    dest='port',
                    help='IRC TCP port',
                    default=6667)

parser.add_argument('--bind',
                    dest='bindip',
                    help='IP used for outgoing IRC connection',
                    default='149.210.200.25')

parser.add_argument('--backend',
                    dest='backendurl',
                    help='URL where the HTTP bot backend can be reached',
                    default='https://www.hackenkunjeleren.nl/bot.php')

parser.add_argument('--channel',
                    dest='homechan',
                    help='IRC channel',
                    default='##hackenkunjeleren')

parser.add_argument('--nickname',
                    dest='nick',
                    help='IRC nickname',
                    default='HKJL')

parser.add_argument('--welcome',
                    dest='welcometext',
                    help='After encountering this string in the IRC server \
                    output, the IRC channel will be joined',
                    default='Welcome to the freenode Internet Relay Chat \
                    Network')

args = parser.parse_args()


def Request(query):
    try:
        r = requests.get(args.backendurl + query)
        r.raise_for_status()
        h = HTMLParser()
        Send(h.unescape(r.text.strip(' \n\t\r')))
    except Exception, e:
        print e


def Send(msg):
    irc.send('PRIVMSG ' + args.homechan + ' :' + msg.encode('utf-8') + '\r\n')


def everyMinute():
    Request('?action=minutecron')


schedule.every(1).minutes.do(everyMinute)

irc = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
irc.bind((args.bindip, 0))
irc.connect((args.network, args.port))
irc.send('NICK ' + args.nick + '\r\n')
irc.send('USER ' + args.nick + ' ' + args.nick + ' ' + args.nick + ' :' +
         args.nick + '\r\n')

while True:
    action = 'none'
    joined = 0
    data = irc.recv(4096)
    print data
    schedule.run_pending()
    if data.find(args.welcometext) != -1 and not joined:
        irc.send('JOIN ' + args.homechan + '\r\n')
        joined = 1
    if data.find('PING') != -1:
        irc.send('PONG ' + data.split()[1] + '\r\n')
    if data.find(args.homechan) != -1:
        Request('?data=' + urllib.quote_plus(data))

