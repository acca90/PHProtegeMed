#!/usr/bin/python

import sys, getopt
import pymysql

def main(argv):
   try:
      opts, args = getopt.getopt(argv,"hi:o:",["ifile=","ofile="])
   except getopt.GetoptError:
      print('test.py -i <inputfile> -o <outputfile>')
      sys.exit(2)
   for opt, arg in opts:
      if opt == '-h':
         print('test.py -i <inputfile> -o <outputfile>')
         sys.exit()
      elif opt in ("-i", "--ifile"):
         inputfile = arg
      elif opt in ("-o", "--ofile"):
         outputfile = arg

if __name__ == "__main__":
   main(sys.argv[1:])



"""
import pymysql

conn = pymysql.connect(host='localhost', port=3306, user='root', passwd='', db='sandbox')

cur = conn.cursor()
cur.execute("SELECT * FROM users")

print(cur.description)
print()

for row in cur:
    print(row)

cur.close()
conn.close()
"""