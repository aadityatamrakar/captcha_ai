import numpy as np
import pandas as pd
from sklearn.externals import joblib
import sys

clf = joblib.load('model.pkl')
data=pd.read_csv('dataset/'+sys.argv[1]).as_matrix()

p = clf.predict( data )
print(p)