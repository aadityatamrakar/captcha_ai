# Captcha Solver
This is simple captcha solver for a particular type of captchas (Shown below).
Eg. 
|  ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/captcha/%20101510.jpg)| ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/captcha/%20104320.jpg) |
|--|--|
| ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/captcha/%20104840.jpg) | ![enter image description here](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/captcha/%20106505.jpg) |

## Demo Video

[![IMAGE ALT TEXT](http://img.youtube.com/vi/p-GUJ60KYbs/0.jpg)](http://www.youtube.com/watch?v=p-GUJ60KYbs "AI Captcha Solver Demo")

## Steps
1. Remove Padding/ Remove Whitespace Area
2. ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/ltr-0.jpg)  ![enter image description here](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/ltr-1.jpg)  ![enter image description here](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/ltr-2.jpg)  ![enter image description here](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/ltr-3.jpg)  ![enter image description here](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/ltr-4.jpg)  ![enter image description here](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/ltr-5.jpg) Break Letter 
3. ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/wltr-0.jpg)  ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/wltr-1.jpg)  ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/wltr-2.jpg)  ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/wltr-3.jpg)  ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/wltr-4.jpg)  ![image](https://raw.githubusercontent.com/aadityatamrakar/captcha_ai/master/steps/wltr-5.jpg)   Rotate Letters 
4. Remove Padding and Stretch to Default Size
5. Convert image to pixel data and send for prediction in python.
6. R F K T G F => Result Captcha 

**Captcha Solver Accuracy - 99.7 %** 
