from flask import Flask, request
import re,imaplib
import datetime
from email import utils
import email
import pytz 
import requests  
import urllib 
from requests import Session
import pandas as pd

token = ""
app = Flask(__name__)

@app.route("/")
def webhook():
	return "Hello!", 200

@app.route("/tbill", methods=['GET'])
def webhook2():
	dfs = pd.read_html("https://www.ccilindia.com/OMMWOL.aspx")
	tz_NY = pytz.timezone('Asia/Kolkata')

	df = dfs[8]
	txt = "<b>TBill - Duration - Return</b>\n"
	n = 0
	for ind in range(len(df[1])):
		try:
			a = datetime.datetime.strptime(df[1][ind],"%d/%m/%Y").replace(tzinfo=tz_NY)
			days  = int((a-datetime.datetime.now(tz_NY)).days)
			price = float(df[6][ind])
			if( ( days < 60 ) and (price > 1) ):
				txt = txt + f"{df[0][ind]} - {days} - {round((100-price)*36400/price/days,2)}% \n"
				n = n + 1
		except Exception as e:
			pass #txt = "\n" + txt + str(e)
	
	txt = txt + "\nhttps://www.ccilindia.com/OMMWOL.aspx"
	txt1 = txt

	if(n>0):
		for chat in list(request.args):
			requests.get("https://api.telegram.org/bot%s/sendmessage?chat_id=%s&parse_mode=HTML&text="%(token,chat)+urllib.parse.quote(txt))

	return txt, 200

# https://github.com/jugaad-py/jugaad-data/tree/master
@app.route("/sgb", methods=['GET'])
def webhook3():
	s = Session()
	h = {
	    "Host": "www.nseindia.com",
	    "Referer": "https://www.nseindia.com/get-quotes/equity?symbol=SBIN",
	    "X-Requested-With": "XMLHttpRequest",
	    "pragma": "no-cache",
	    "sec-fetch-dest": "empty",
	    "sec-fetch-mode": "cors",
	    "sec-fetch-site": "same-origin",
	    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36",
	    "Accept": "*/*",
	    "Accept-Encoding": "gzip, deflate, br",
	    "Accept-Language": "en-GB,en-US;q=0.9,en;q=0.8",
	    "Cache-Control": "no-cache",
	    "Connection": "keep-alive",
	    }
	s.headers.update(h)
	s.get("https://www.nseindia.com/get-quotes/equity?symbol=LT")
	r = s.get("https://www.nseindia.com/api/sovereign-gold-bonds")
	#print(r.json()["data"])

	df = pd.json_normalize(r.json()["data"])
	df = df.sort_values('ltP')
	df.reset_index(drop = True, inplace = True)
	# df.to_csv("g.csv")

	n = 0
	txt = "<b>Symbol - LTP - Vol.</b>\n"
	for j in range(5):
		if(int(df["qty"][j]) > 25):
			txt = txt + f'• {df["symbol"][j]} - {df["ltP"][j]} - {df["qty"][j]}\n'
			n = n + 1

	s = requests.get("https://ibjarates.com/").content
	a = re.findall('"lblrate24K">.*</span>',s.decode('utf-8'))[0]
	a = float(a.split(">")[1].split("<")[0])
	txt = txt + f"\n<i> Today's Rate : <b>{a}</b></i>"

	if(n > 0):
		for chat in list(request.args):
			requests.get("https://api.telegram.org/bot%s/sendmessage?chat_id=%s&parse_mode=HTML&text="%(token,chat)+urllib.parse.quote(txt))

	return txt, 200


app.run()