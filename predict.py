import numpy as np
import pandas as pd
from sklearn.externals import joblib
import sys

clf = joblib.load('model.pkl')
data=pd.read_csv('predict/'+sys.argv[1]+'.csv',header=None).as_matrix()

p = clf.predict( data )
print(p)

file = open("result.txt", "w")
file.write(p)
file.close()