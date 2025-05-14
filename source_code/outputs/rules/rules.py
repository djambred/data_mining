def findDecision(obj): #obj[0]: outlook, obj[1]: temp, obj[2]: humidity, obj[3]: windy
   # {"feature": "outlook", "instances": 14, "metric_value": 0.9403, "depth": 1}
   if obj[0] == 'Rainy':
      # {"feature": "windy", "instances": 5, "metric_value": 0.971, "depth": 2}
      if obj[3]<=False:
         return 'Yes'
      elif obj[3]>False:
         return 'No'
      else:
         return 'Yes'
   elif obj[0] == 'Sunny':
      # {"feature": "humidity", "instances": 5, "metric_value": 0.971, "depth": 2}
      if obj[2] == 'High':
         return 'No'
      elif obj[2] == 'Normal':
         return 'Yes'
      else:
         return 'No'
   elif obj[0] == 'Overcast':
      return 'Yes'
   else:
      return 'Yes'
