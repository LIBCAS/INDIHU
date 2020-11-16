<?php

namespace WP\Services;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Elasticsearch\Client;

class ElasticSearch{

    const TYPE_PAGE = 'page';
    const TYPE_POST = 'post';
    const TYPE_JOB_OFFER = 'job_offer';
	const TYPE_EMPLOYEE = 'employee';
	const TYPE_APPEAL = 'appeal';

	/** @var Client */
	private $client = null;

	/** @var string */
	private $host;

	/** @var string */
	private $table;

	/** @var string */
	private $index;
	
	public function __construct(string $host, string $index, string $table){
		$this->host = $host;
		$this->index = $index;
		$this->table = $table;
	}

	/**
	 * @return void
	 */
	private function initElastic() : void{

		if(!$this->client){
			$this->client = ClientBuilder::create()->setHosts([$this->host])->build();
		}

		if(!$this->indexExists()){
			$this->createIndex();
		}
	}

	/**
	 * @return boolean
	 */
    private function indexExists() : bool{
		try{
			$result = $this->client->indices()->exists(["index" => $this->index]);
			return $result;
		}catch(\Exception $e){
			\Tracy\Debugger::log($e->getMessage());
			return false;
		}
    }

	/**
	 * @return void
	 */
    private function createIndex() : void{
		$params = [
			'index' => $this->index,
			'body' => [
				'settings' => [
					'analysis' => [
						'filter' => [
							'czech_hunspell' => [
								'type' => 'hunspell',
								'locale' => 'cs_CZ'
							],
							'czech_stop' => [
								'type' => 'stop',
								'stopwords' => ['že', '_czech_']
							],
							'unique_on_same_position' => [
								'type' => 'unique',
								'only_on_same_position' => true
							]
						],
						'analyzer' => [
							'czech' => [
								'type' => 'custom',
								'tokenizer' => 'standard',
								'filter' => [
									'czech_stop',
									'czech_hunspell',
									'lowercase',
									'czech_stop',
									'icu_folding',
									'unique_on_same_position'	
								]
							]
						]
					]
				],
				"mappings" => [
					$this->table => [
						'properties' => $this->getMappings(),
					]
				]
			]
		];

		try{
			$this->client->indices()->create($params);
		}catch(NoNodesAvailableException $e){
			\Tracy\Debugger::log($e->getMessage());
		}
    }

	/**
	 * @return array
	 */
    private function getMappings() : array{
		$mappings = [
            'suggest' => [
				'type' => 'completion'
			],
			'title' => [
				'type' => 'text',
				'analyzer' => 'czech'
			],
			'description' => [
				'type' => 'text',
				'analyzer' => 'czech'
			],
			'date_publish' => [
				'type' => 'date',
				'format' => 'YYYY-MM-dd HH:mm:ss'
			],
			'url' => [
				'type' => 'keyword'
			],
			'tags' => [
				'type' => 'keyword'
			],
			'prosecution' => [
				'type' => 'integer'
			],
			'prosecution_name' => [
				'type' => 'keyword'
			],
			'lang' => [
				'type' => 'keyword'
			],
			'file_number_1' => [
				'type' => 'text'
			],
			'file_number_2' => [
				'type' => 'text'
			],
			'file_number_3' => [
				'type' => 'text'
			]
		];

		return $mappings;
	}
	
	/**
	 * @param integer $id
	 * @param string $type
	 * @return array
	 */
    public function get(int $id, string $type) : ?array{

        $params = [
			'index' => $this->index,
			'type' => $this->table,
			'id' => $type . '_' . $id,
		];
		
		try{
			$response = $this->client->get($params);
			return $response;
		}catch(Missing404Exception $e){
			return null;
		}

    }
	
	/**
	 * @param object $object
	 * @param integer $id
	 * @param string $type
	 * @return boolean
	 */
    public function index(array $data, int $id, string $type) : bool{

		try{
			$this->initElastic();

			$elasticObject = $this->get($id, $type);

			$params = [
				'index' => $this->index,
				'type' => $this->table,
				'id' => $type . '_' . $id,
			];
			
			if($elasticObject){
				// update
				$params['body'] = ['doc' => $data];
				$this->client->update($params);
			}else{
				//insert
				$params['body'] = $data;
				$this->client->index($params);
			}

		}catch(\Exception $e){
			\Tracy\Debugger::log($e->getMessage());
			return false;
		}

		return true;
	}
	
	/**
	 * @param integer $id
	 * @param string $type
	 * @return boolean
	 */
    public function delete(int $id, string $type) : bool{

		try{
			$this->initElastic();

			$params = [
				'index' => $this->index,
				'type' => $this->table,
				'id' => $type . '_' . $id,
			];

			$this->client->delete($params);
			
		}catch(\Exception $e){
			\Tracy\Debugger::log($e->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * @param string $type
	 * @return boolean
	 */
	public function deleteType(string $type) : bool{

		try{
			$this->initElastic();

			$params = [
				'index' => $this->index,
				'type' => $this->table,
				'body' => [
					'query' => [
						'match' => [
							'type' => $type
						]
					]
				]
			];

			$this->client->deleteByQuery($params);

		}catch(\Exception $e){
			\Tracy\Debugger::log($e->getMessage());
			return false;
		}

		return true;
	}


    public function search($filter, $limit, $offset){

		$pluginsWeight = [
			self::TYPE_PAGE => 20,
			self::TYPE_POST => 2,
			self::TYPE_JOB_OFFER => 2,
			self::TYPE_EMPLOYEE => 4
		];

		$pluginsWeightArray = [];
		foreach($pluginsWeight as $name => $weight){
			$pluginsWeightArray[] = [
				'filter' => [
					'term' => [
						'type' => $name
					]
				],
				'weight' => $weight
			];
		}

		$queryParams = [
			'function_score' => [
				'score_mode' => "sum",
				'boost_mode' => 'multiply',
				'functions' => [
					$pluginsWeightArray[0],
					$pluginsWeightArray[1],
					$pluginsWeightArray[2],
					$pluginsWeightArray[3],
					['weight' => 1],
					[
						'weight' => 10,
						'gauss' => [
							'date_publish' => [
								'origin' => date('Y-m-d H:i:s'),
								'scale' => '10d',
								'decay' => 0.5
							]
						]
					],[
						'weight' => 6,
						'gauss' => [
							'date_publish' => [
								'origin' => date('Y-m-d H:i:s'),
								'scale' => '30d',
								'decay' => 0.5
							]
						]
					],[
						'weight' => 2,
						'gauss' => [
							'date_publish' => [
								'origin' => date('Y-m-d H:i:s'),
								'scale' => '60d',
								'decay' => 0.5
							]
						]
					]
				],
				'query' => [
					'bool' => [
						'must' => [
							'multi_match' => [
								'query' => $filter['s'],
								'analyzer' => 'czech',
								'fields' => [
									'title^2',
									'description^0.1'
								],
								"fuzziness" => 1
							]
						],
						'filter' => [
							'term' => [
								'lang' => 'cs',
							]
						]
					]
				]
			]
		];

		if(isset($filter['type']) && $filter['type'] != 'all'){

			$queryFilter = [
				'term' => [
					'type' => $filter['type']
				]
			];

			$queryParams['function_score']['query']['bool']['filter'] = $queryFilter;
		}
		
		if(isset($filter['prosecution']) && $filter['prosecution'] != 'all'){

			$queryFilter = [
				'term' => [
					'prosecution' => $filter['prosecution']
				]
			];

			$queryParams['function_score']['query']['bool']['filter'] = $queryFilter;
		}

		$highlight = array(
			"pre_tags"  => [
				'<span class="search-results__highlight">'
			],
			"post_tags" => [
				'</span>'
			],
			'fields' => [
				'title' => new \stdClass(),
				'description' => new \stdClass()
			]
		);

       	$params['index'] = $this->index;
		$params['size'] = $limit;
		$params['from'] = $offset;
		$params['body']['query'] = $queryParams;
		$params['body']['highlight'] = $highlight;
		
        try{
			$this->initElastic();

            $query = $this->client->search($params);
        }catch(\Exception $e){
			\Tracy\Debugger::log($e->getMessage());
			bdump($e->getMessage());
            return [];
        }

        if($query['hits']['total'] >= 1){
            return $query['hits'];
        }else{
            return [];
        }
        
    }

    public function searchAutocomplete($findString){

		$queryParams = [
			'match' => [
                'testField' => $findString
            ]
		];

		$suggestParams = [
			'item-suggest-fuzzy' => [
				'text' => $findString,
				'completion' => [
					'field' => 'suggest',
					'size' => 5,
					'fuzzy' => [
						'fuzziness' => 1
					]
				]
			]
		];
		
       	$params['index'] = $this->index;
		$params['body']['query'] = $queryParams;
		$params['body']['suggest'] = $suggestParams;
		
        try{
			$this->initElastic();

            $query = $this->client->search($params);
        }catch(\Exception $e){
			\Tracy\Debugger::log($e->getMessage());
			bdump($e->getMessage());
            return [];
		}
		
		if($query['suggest']['item-suggest-fuzzy'][0]['options'] >= 1){
            return $query['suggest']['item-suggest-fuzzy'][0]['options'];
        }else{
            return [];
        }
	}

	// public function newSearch($filter, $limit, $offset){

	// 	$queryParams = [
	// 		"bool" => [
	// 			"should" => [
	// 				[
	// 					"query_string" => [
	// 						"fields"=> [
	// 							"file_number_1"
	// 						],
	// 						"query" => "5114\\/",
	// 						"analyzer" => "keyword"
	// 					]
	// 				],[
	// 					"wildcard" => [
	// 						"file_number_2" => "*5114/2020*"
	// 					]
	// 				],[
	// 					"wildcard" => [
	// 						"file_number_3" => "*5114/2020*"
	// 					]
	// 				],[
	// 					"match"=> [
	// 						"description" => "5114/2020"
	// 					]
	// 				]
	// 			]
	// 		]
	// 	];

    //    	$params['index'] = $this->index;
	// 	$params['size'] = $limit;
	// 	$params['from'] = $offset;
	// 	$params['body']['query'] = $queryParams;
		
    //     try{
	// 		$this->initElastic();

    //         $query = $this->client->search($params);
    //     }catch(\Exception $e){
	// 		\Tracy\Debugger::log($e->getMessage());
	// 		bdump($e->getMessage());
    //         return [];
    //     }

    //     if($query['hits']['total'] >= 1){
    //         return $query['hits'];
    //     }else{
    //         return [];
    //     }
        
    // }
}