
## Install
```bash
$ cd Elasitc-pwd
$ sudo bin/elasticsearch-plugin install ingest-geoip

```

## setting up the Pipline
```bash
PUT _ingest/pipeline/geoip
{
  "description" : "Add geoip info",
  "processors" : [
    {
      "geoip" : {
        "field" : "ip"
      }
    }
  ]
}
```

## Index And Mapping
```bash
PUT /test_geoip
{
  "mappings": {
    "doc": {
      "properties": {
        "geoip": {
          "properties": {
            "location": {
              "type": "geo_point"
            }
          }
        },
        "service":{
            "type":"keyword",
            "index":"not_analyzed"
        }
      }
    }
  }
}
```
## put document
```bash
POST /test_geoip/doc/?pipeline=geoip
{
    "service":"user",

    "ip":"122.14.45.43"
}

GET /test_geoip/_search
{
  "took": 0,
  "timed_out": false,
  "_shards": {
    "total": 5,
    "successful": 5,
    "failed": 0
  },
  "hits": {
    "total": 1,
    "max_score": 1,
    "hits": [
      {
        "_index": "test_geoip",
        "_type": "doc",
        "_id": "AWfPhEPmXRdKyVS0Qlns",
        "_score": 1,
        "_source": {
          "geoip": {
            "continent_name": "Asia",
            "city_name": "Beijing",
            "country_iso_code": "CN",
            "region_name": "Beijing",
            "location": {
              "lon": 116.3883,
              "lat": 39.9289
            }
          },
          "service": "user",
          "ip": "122.14.45.43"
        }
      }
    ]
  }
}
```
