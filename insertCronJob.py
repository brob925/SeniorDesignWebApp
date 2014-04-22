import urllib2
import random

response = urllib2.urlopen('http://localhost/seniordesign/insertNode2.php?nodeid=1')
response = urllib2.urlopen('http://localhost/seniordesign/insertNode2.php?nodeid=2')
response = urllib2.urlopen('http://localhost/seniordesign/insertNode2.php?nodeid=3')

temperature = [];
latitude = [];
longitude = [];
radiation = [];
carbonDioxide = [];
for i in range(0,3):
    randTemp = random.randint(700, 800);
    randTemp = randTemp/10.0;
    temperature.append(randTemp);
    randLat = random.randint(30629, 30639);
    randLat = randLat/1000.0;
    latitude.append(randLat);
    randLong = random.randint(96329, 96339);
    randLong = -randLong/1000.0;
    longitude.append(randLong);
    randRad = random.randint(13500, 14150);
    randRad = randRad/10.0;
    radiation.append(randRad);
    randCo2 = random.randint(3900, 4150);
    randCo2 = randCo2/10.0;
    carbonDioxide.append(randCo2);

print temperature;
print latitude;
print longitude;
print radiation;
print carbonDioxide;

for i in range (0,3):
    queryString = 'nodeid='+str((i+1))+'&temp='+str(temperature[i])+'&lat='+str(latitude[i])+'&long='+str(longitude[i])+'&rad='+str(radiation[i])+'&co2='+str(carbonDioxide[i]);
    urllib2.urlopen('http://localhost/seniordesign/insertReadings2.php?'+queryString);