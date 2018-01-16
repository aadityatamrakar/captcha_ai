import numpy as np
import matplotlib.pyplot as pt
import pandas as pd
from sklearn_porter import Porter
from sklearn.tree import DecisionTreeClassifier
from sklearn.externals import joblib

data=pd.read_csv('dataset/d2.csv').as_matrix()
clf=DecisionTreeClassifier()
print(data[1])
# Training Dataset
xtrain=data[0:890, 1:]
print(xtrain[1])
train_label=data[0:890,0]
clf.fit(xtrain, train_label)

# Export:
# porter = Porter(clf, language='js')
# output = porter.export(embed_data=True)
# print(output)

joblib.dump(clf, 'model.pkl')