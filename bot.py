import requests
import socket
import urllib
import schedule
import argparse
import html

parser = argparse.ArgumentParser(
  description='Run the Hackenkunjeleren IRC bot',
  formatter_class=argparse.ArgumentDefaultsHelpFormatter)

parser.add_argument('--network',
                    dest='network',
                    help='IRC server hostname or IP',
                    default='irc.smurfnet.ch')

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
                    default='#hackenkunjeleren')

parser.add_argument('--nickname',
                    dest='nick',
                    help='IRC nickname',
                    default='HKJL')

parser.add_argument('--welcome',
                    dest='welcometext',
                    help='After encountering this string in the IRC server \
                    output, the IRC channel will be joined',
                    default='End of message of the day')

args = parser.parse_args()


def Request(query):
    try:
        r = requests.get(args.backendurl + query)
        r.raise_for_status()
        Send(html.unescape(r.text.strip(' \n\t\r')))
    except Exception as e:
        print(e)


def Send(msg):
    irc.send(('PRIVMSG ' + args.homechan + ' :' + msg + '\r\n').encode())


def everyMinute():
    Request('?action=minutecron')


schedule.every(1).minutes.do(everyMinute)

irc = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
irc.bind((args.bindip, 0))
irc.connect((args.network, args.port))
irc.send(('NICK ' + args.nick + '\r\n').encode())
irc.send(('USER ' + args.nick + ' ' + args.nick + ' ' + args.nick + ' :' +
         args.nick + '\r\n').encode())

while True:
    action = 'none'
    joined = 0
    data = irc.recv(4096).decode()
    print(data)
    schedule.run_pending()
    if data.find(args.welcometext) != -1 and not joined:
        irc.send(('JOIN ' + args.homechan + '\r\n').encode())
        joined = 1
    if data.find('PING') != -1:
        irc.send(('PONG ' + data.split()[1] + '\r\n').encode())
    if data.find(args.homechan) != -1:
        Request('?data=' + urllib.parse.quote_plus(data))

