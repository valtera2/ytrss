#!/usr/bin/env python3
import configparser,urllib.request,ssl

sslContext = ssl.SSLContext(ssl.PROTOCOL_TLSv1_2)
baseUrl = 'https://www.youtube.com/feeds/videos.xml?channel_id='
#baseUrl = 'http://localhost:8099/'
config = configparser.ConfigParser()
config.read('extract.ini')
for sect in config.sections():
	outFile = config.get(sect, 'path')
	id = config.get(sect, 'id')
	res = urllib.request.urlopen(baseUrl + id, context = sslContext)
	res = res.read()
	try:
		f = open(outFile, 'wb')
	except:
		print ("Cannot open file for writing")
	f.write(res)
	f.close()
	print ("Xml for Id" + id + " refreshed")
