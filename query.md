# Examples of json-rpc queries

## Register contract provider

### Request

```json
{
  "jsonrpc": "2.0",
  "method": "registerContract",
  "params": {
    "schemes": [
      {
        "in": true,
        "scheme": {
          "type": "object",
          "properties": {
            "a": {
              "type": "string"
            }
          },
          "required": [
            "a"
          ]
        },
        "type": "json-schema"
      }
    ],
    "service": {
      "name": "provider",
      "address": [

      ],
      "check_url": "http://localhost:8881/provider.json"
    }
  },
  "id": 1
}
```

## Register contract user

### Request

```json
{
  "jsonrpc": "2.0",
  "method": "registerUsage",
  "params": {
    "schemes": [
      {
        "in": true,
        "scheme": {
          "type": "object",
          "properties": {
            "a": {
              "type": "string",
              "maxLength": 50
            }
          },
          "required": [
            "a"
          ]
        },
        "type": "json-schema"
      }
    ],
    "usage": {
      "name": "usage",
      "address": [

      ],
      "check_url": "http://localhost:8882/usage.json"
    }
  },
  "id": 1
}
```

## Get all

### Request

```json
{
	"jsonrpc": "2.0", 
	"method": "getAll", 
	"params": {}, 
	"id": 1
}
```

## Get

### Request

```json
{
  "jsonrpc": "2.0",
  "method": "get",
  "params": {
    "schemes": [
      {
        "in": true,
        "scheme": {
          "type": "object",
          "properties": {
            "a": {
              "type": "string"
            }
          },
          "required": [
            "a"
          ]
        },
        "type": "json-schema"
      }
    ]
  },
  "id": 1
}
```

## Remove contract

### Request

```json
{
  "jsonrpc": "2.0",
  "method": "removeContract",
  "params": {
    "schemes": [
      {
        "in": true,
        "scheme": {
          "type": "object",
          "properties": {
            "a": {
              "type": "string"
            }
          },
          "required": [
            "a"
          ]
        },
        "type": "json-schema"
      }
    ],
    "service": {
      "name": "provider",
      "address": [

      ],
      "check_url": "http://localhost:8881/provider.json"
    }
  },
  "id": 1
}
```

## Remove usage

### Request

```json
{
  "jsonrpc": "2.0",
  "method": "removeUsage",
  "params": {
    "schemes": [
      {
        "in": true,
        "scheme": {
          "type": "object",
          "properties": {
            "a": {
              "type": "string",
              "maxLength": 50
            }
          },
          "required": [
            "a"
          ]
        },
        "type": "json-schema"
      }
    ],
    "usage": {
      "name": "usage",
      "address": [

      ],
      "check_url": "http://localhost:8882/usage.json"
    }
  },
  "id": 1
}
```

## Find contracts with users and without providers

### Request

```json
{
  "jsonrpc": "2.0",
  "method": "getProblems",
  "params": {},
  "id": 1
}
```

## Find providers for contract

```json
{
  "jsonrpc": "2.0",
  "method": "resolve",
  "params": {
    "schemes": [
      {
        "in": true,
        "scheme": {
          "properties": {
            "a": {
              "type": "string",
              "maxLength": 50
            }
          },
          "required": [
            "a"
          ],
          "type": "object"
        },
        "type": "json-schema"
      }
    ]
  },
  "id": 1
}
```

## Can contract provider can be removed without consequences

### Request

```json
{
  "jsonrpc": "2.0",
  "method": "isRemoval",
  "params": {
    "schemes": [
      {
        "in": true,
        "scheme": {
          "type": "object",
          "properties": {
            "a": {
              "type": "string"
            }
          },
          "required": [
            "a"
          ]
        },
        "type": "json-schema"
      }
    ],
    "service": {
      "name": "provider",
      "address": [

      ],
      "check_url": "http://localhost:8881/provider.json"
    }
  },
  "id": 1
}
```