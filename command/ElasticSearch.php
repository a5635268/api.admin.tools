<?php

namespace app\command;

use app\common\command\Base;
use Elasticsearch\ClientBuilder;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use libs\Log;
use think\Db;
use Exception;

/**
 *
 * Class ElasticSearch
 * @package app\command
 */
class ElasticSearch extends Base
{
    private $es;

    protected function configure()
    {
        $this->setName('ElasticSearch')
            ->addOption('test', 't', Option::VALUE_NONE, 'this is a value_none option')
            ->addOption('required', null, Option::VALUE_REQUIRED, 'this is a value_required option')
            ->addOption('optional', null, Option::VALUE_OPTIONAL, 'this is a value_optional option')
            // VALUE_IS_ARRAY 暂未支持该方法
            // ->addOption('isarray', null, Option::VALUE_IS_ARRAY, 'this is a value_is_array option')
            // 必选参数一定要在可选参数之前
            // ->addArgument('required', Argument::REQUIRED, "argument::required")
            ->addArgument('optional', Argument::OPTIONAL, "argument::optional")
            // 暂未支持数组
            //->addArgument('array', Argument::IS_ARRAY, " argument::is_array")
            ->setDescription('this is a description');
    }

    private function init()
    {
        // 可以配置多节点
        $hosts = [
            '172.28.3.199:9200',
        ];
        $client = ClientBuilder::create()           // Instantiate a new ClientBuilder
        ->setHosts($hosts)      // Set the hosts
        ->build();              // Build the client object
        $this->es = $client;
    }

    protected function execute(Input $input , Output $output)
    {
        $this->init();
        return $this->test();
        $arguments =  array_filter($input->getArguments(true));
        if (empty($arguments)) {
            return $output->error('please enter $arguments ^_^');
        }
        $options = array_filter($input->getOptions(true));
        if (empty($options)) {
            return $output->error('please enter options ^_^');
        }
        try {
            $input->getOption('test') && $this->test();
        } catch (Exception $ex) {
            return Log::err(__METHOD__ , $options , $ex->getMessage());
        }
    }


    public function test()
    {
        return $this->searchDoc();
        return $this->scroll();
        return $this->addDoc();
        return $this->createIndex();
    }

    /**
     * 创建索引
     * 索引管理：https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_index_management_operations.html
     */
    private function createIndex()
    {
        $params = [
            // database name
            'index' => 'longya',
            'body' => [
                'settings' => [
                    'number_of_shards' => 5,
                    // 副本分片创建后还可以更改
                    'number_of_replicas' => 1,
                    'analysis' => [
                        // 自定义的分词器
                        "tokenizer" => [
                            // https://github.com/medcl/elasticsearch-analysis-pinyin
                            "my_pinyin" => [
                                "type"                       => "pinyin" ,
                                "keep_original"              => true , // 保留原始输入
                                "keep_joined_full_pinyin"    => true, // 拼音可以紧凑在一起， 刘德华 > [liudehua],
                                "remove_duplicated_term"     => true // 去掉重复，de的>de
                            ]
                        ],
                        // 引用自定义的分词器,这里主要是拼音分词
                        "analyzer" => [
                            // 设置好以后在mapping里面直接引用pinyin_analyzer就可以了
                            "pinyin_analyzer" => [
                                "tokenizer" =>  "my_pinyin"
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    // dynamic template
                    /*'_default_' => [
                        'properties' => []
                    ],*/
                    
                    // table name;
                    'games' => [
                        'properties' => [
                            'game_id' => [
                                'type' => 'integer'
                            ],
                            'game_name' => [
                                'type' => 'text',
                                // 指定了三个分词器
                                'fields' => [
                                    "cn"     => [
                                        "type"     => "text" ,
                                        "analyzer" => "ik_smart"
                                    ] ,
                                    "pinyin" => [
                                        "type"        => "text" ,
                                        // 不存入_source里面
                                        "store"       => false ,
                                        "term_vector" => "with_offsets" ,
                                        "analyzer"    => "pinyin_analyzer" ,
                                        "boost"       => 10
                                    ] ,
                                    // 搜英文的时候用到
                                    "en"     => [
                                        "type"     => "text" ,
                                        "analyzer" => "english"
                                    ]
                                ]
                            ],
                            'profile' => [
                                'type' => 'text',
                                "analyzer" => 'ik_smart'
                            ],
                            'comment_num' => [
                                'type' => 'integer'
                            ],
                            'comment_score' => [
                                'type' => 'float'
                            ],
                            'topic_num' => [
                                'type' => 'integer'
                            ],
                            'follow_num' => [
                                'type' => 'integer'
                            ],
                            'create_time' => [
                                'type' => 'date',
                                // 默认的，可以不写
                                'format' => 'yyyy-MM-dd HH:mm:ss',
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $response = $this->es->indices()->create($params);
        // 返回的是个数组
        return $response;
    }

    /**
     * 创建文档
     * https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_indexing_documents.html
     */
    private function addDoc()
    {
        $games = Db::table('tb_game')
            ->field('game_id,game_name,game_profile profile,alias,comment_num,comment_score,follow_num,topic_num,create_time')
            ->cursor();

        $params = ['body' => []];
        foreach ($games as $item){
            // 单笔创建
            /*$params = [
                'index' => 'longya',
                'type' => 'games',
                'id' => $item['game_id'],
                'body' => [
                    'comment_num' => $item['comment_num'],
                    'game_id' => $item['game_id'],
                    'game_name' => $item['game_name'],
                    'profile' => $item['profile'],
                    'alias' => $item['alias'] ? explode(',',$item['alias']) : [],
                    'comment_score' => $item['comment_score'],
                    'follow_num' => $item['follow_num'],
                    'topic_num' => $item['topic_num'],
                    'create_time' => date('Y-m-d H:i:s',$item['create_time'])
                ]
            ];
            $response = $this->es->index($params);*/
            // 批量创建
            $params['body'][] = [
                'index' => [
                    '_index' => 'longya',
                    '_type' => 'games',
                    '_id' => $item['game_id']
                ]
            ];
            $params['body'][] = [
                'comment_num' => $item['comment_num'],
                'game_id' => $item['game_id'],
                'game_name' => $item['game_name'],
                'profile' => $item['profile'],
                'alias' => $item['alias'] ? explode(',',$item['alias']) : [],
                'comment_score' => $item['comment_score'],
                'follow_num' => $item['follow_num'],
                'topic_num' => $item['topic_num'],
                'create_time' => date('Y-m-d H:i:s',$item['create_time'])
            ];
        }
        $responses = $this->es->bulk($params);
        dd($responses);
    }

    /**
     * 搜索文档
     * https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_search_operations.html
     */
    private function searchDoc()
    {
        // 原生字符串查询
       /* $json = '{
          "query":{
            "bool":{
              "should":[
                {"match":{"game_name":"枪战"}},
                {"match":{"profile":"枪战"}},
                {"term":{"alias.keyword":"枪战"}}
              ]
            }
          },
          "highlight" : {
                "pre_tags" : ["<em>","<span>"],
                "post_tags" : ["</em>","</span>"],
                "fields" : {
                    "*" : {}
                }
            },
          "from":0,
          "size":10
        }';
        $params = [
            'index' => 'longya',
            'type' => 'games',
            'body' => $json
        ];
        $results = $this->es->search($params);*/
        $keyword = '枪战';
        $isEn = preg_match('/^[A-Za-z]+$/',$keyword);
        $query = [
            "bool" => [
                "should" => [
                    ["term" => ["alias.keyword" => $keyword]],
                    ["match" => ["game_name" => $keyword]] ,
                    ["match" => ["profile" => $keyword]]
                ]
            ]
        ];
        // 如果是全字母的就有可能是拼音或者英语
        if($isEn){
            $query['bool']['should'][1]['match'] = ['game_name.pinyin' => $keyword];
            $query['bool']['should'][2]['match'] = ['game_name.en' => $keyword];
        }

        // 自定义高亮字段
        $highlight = [
            "pre_tags"  => ["<span>"] ,
            "post_tags" => ["</span>"] ,
            "fields"    => [
                // "*" 表示所有被查询出来的字段都要加上tags
                "*" =>  new \stdClass()
            ]
        ];

        // 定义分页
        $from = 0;
        $size = 20;

        // 定义排序
        $sort = [
            'game_id' => [
                'order' => 'desc'
            ]
        ];

        $params = [
            'index' => 'longya',
            'type' => 'games',
            'body' => [
                'query' => $query,
                'highlight' => $highlight,
                'from' => $from,
                'size' => $size,
                'sort' => $sort
            ]
        ];
        try {
            $results = $this->es->search($params);
        } catch (Exception $ex) {
            $r = json_decode($ex->getMessage());
            dd($r);
        }
        dd($results);
    }

    private function scroll()
    {
        $params = [
            "scroll" => "30s",
            "size" => 50,
            "index" => "longya",
            "body" => [
                "query" => [
                    "match_all" => new \stdClass()
                ]
            ]
        ];
        $response = $this->es->search($params);

        // Now we loop until the scroll "cursors" are exhausted
        while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {

            // **
            // Do your work here, on the $response['hits']['hits'] array
            // 这里配合bulk可以高效的做索引迁移，一般用于要更改有大量文档的索引的mapping
            // **

            // When done, get the new scroll_id
            // You must always refresh your _scroll_id!  It can change sometimes
            $scroll_id = $response['_scroll_id'];

            // Execute a Scroll request and repeat
            $params = [
                "scroll_id" => $scroll_id,  //...using our previously obtained _scroll_id
                "scroll" => "30s"           // and the same timeout window
            ];
            $response = $this->es->scroll($params);
            d($response);
        }
    }
}
