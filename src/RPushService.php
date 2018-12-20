<?php
namespace Lgy\RPush;

use JPush\Client as JPushClient;
use JPush\Exceptions\APIConnectionException;
use JPush\Exceptions\APIRequestException;

class RPushService {
    /**
     * @var
     */
    private $jPushClient;


    /**
     * RPushService constructor.
     * @param $appKey
     * @param $masterSecret
     * @param $logFile
     */
    public function __construct($appKey, $masterSecret, $logFile)
    {
        $this->jPushClient = new JPushClient($appKey,$masterSecret,$logFile=null);
    }

    /**
     * 推送所有设备
     * @return string
     */
    public function allPush($content)
    {
        try {
            $this->jPushClient->push()
                ->setPlatform(['ios', 'android'])   //推送平台设置,目前支持 ios, android, winphone
                ->addAllAudience()
                ->setNotificationAlert($content) //通知内容体。是被推送到客户端的内容。与 message 一起二者必须有其一，可以二者并存
                //应用内消息。或者称作：自定义消息，透传消息。是被推送到客户端的内容。与 notification 一起二者必须有其一，可以二者并存
                ->message('message content', array(
                    'title' => 'hello jpush',
                    'content_type' => 'text',
                    'extras' => array(
                        'key' => 'value',
                        'jiguang'
                    ),
                ))
                ->options(array(
                    // sendno: 表示推送序号，纯粹用来作为 API 调用标识，
                    // API 返回时被原样返回，以方便 API 调用方匹配请求与返回
                    'sendno' => 100,

                    // time_to_live: 表示离线消息保留时长(秒)，
                    // 推送当前用户不在线时，为该用户保留多长时间的离线消息，以便其上线时再次推送。
                    // 默认 86400 （1 天），最长 10 天。设置为 0 表示不保留离线消息，只有推送当前在线的用户可以收到
                    // 这里设置为 1 仅作为示例

                    // 'time_to_live' => 1,

                    // apns_production: 表示APNs是否生产环境，
                    // True 表示推送生产环境，False 表示要推送开发环境；如果不指定则默认为推送生产环境

                    'apns_production' => true,

                    // big_push_duration: 表示定速推送时长(分钟)，又名缓慢推送，把原本尽可能快的推送速度，降低下来，
                    // 给定的 n 分钟内，均匀地向这次推送的目标用户推送。最大值为1400.未设置则不是定速推送
                    // 这里设置为 1 仅作为示例

                    // 'big_push_duration' => 1
                ))
                ->send();
        } catch (APIConnectionException $e) {
            return $e->getMessage();
        } catch (APIRequestException $e) {
            return $e->getMessage();
        }
    }

    /**
     * 别名推送
     * @param $uid int 用户Id(别名)
     * @param $content
     * @param $type
     * @param $vid
     * @return string
     */
    public function aliasPush($uid, $content , $type ='', $vid ='')
    {
        try {
            if (is_array($uid)){
                $uids = $uid;
            }else{
                $uids = [(string)$uid];
            }
            $this->jPushClient->push()
                ->setPlatform(['ios', 'android'])
                ->addAlias($uids)
                //->setNotificationAlert($content)
                ->iosNotification($content, [
                    'sound'     => 'xxx',
                    'badge'     => '+1',
                    'extras'    => [
                        'type'  => $type,
                        'id'   => $vid
                    ]
                ])
                ->androidNotification($content, [
                    'title'         => 'xxx',
                    'builder_id'    => '1',
                    'extras'        => [
                        'type'      => $type,
                        'id'        => $vid
                    ]
                ])
                ->message('message content', array(
                    'title' => $content,
                    'content_type' => 'text',
                    'extras' => array(
                        'url' => $type,
                    ),
                ))
                ->options(array(
                    // sendno: 表示推送序号，纯粹用来作为 API 调用标识，
                    // API 返回时被原样返回，以方便 API 调用方匹配请求与返回
                    'sendno' => 100,

                    // time_to_live: 表示离线消息保留时长(秒)，
                    // 推送当前用户不在线时，为该用户保留多长时间的离线消息，以便其上线时再次推送。
                    // 默认 86400 （1 天），最长 10 天。设置为 0 表示不保留离线消息，只有推送当前在线的用户可以收到
                    // 这里设置为 1 仅作为示例

                    // 'time_to_live' => 1,

                    // apns_production: 表示APNs是否生产环境，
                    // True 表示推送生产环境，False 表示要推送开发环境；如果不指定则默认为推送生产环境

                    'apns_production' => false,

                    // big_push_duration: 表示定速推送时长(分钟)，又名缓慢推送，把原本尽可能快的推送速度，降低下来，
                    // 给定的 n 分钟内，均匀地向这次推送的目标用户推送。最大值为1400.未设置则不是定速推送
                    // 这里设置为 1 仅作为示例

                    // 'big_push_duration' => 1
                ))
                ->send();
        } catch (APIConnectionException $e) {
            return $e->getMessage();
        } catch (APIRequestException $e) {
            return $e->getMessage();
        }
    }

    /**
     * 定时推送
     * @param $time
     */
    public function schedulePush($time)
    {
        $payload = $this->schedulePayLoad();

        $response = $this->singleSchedule($payload, $time);
        $option = array(
            "start" => "2016-12-22 13:45:00",
            "end" => "2016-12-25 13:45:00",
            "time" => "14:00:00",
            "time_unit" => "DAY",
            "frequency" => 1
        );
        $response = $this->periodicalSchedule($payload, $option);
    }

    /**
     * 定时发送预载
     * @return array
     */
    private function schedulePayLoad()
    {
        return $this->jPushClient->push()
            ->setPlatform("all")
            ->addAllAudience()
            ->setNotificationAlert("Hi, 这是一条定时发送的消息")
            ->build();
    }

    /**
     * 定时发送单次
     * @param $payload
     * @param $time
     */
    private function singleSchedule($payload, $time)
    {
        $this->jPushClient->schedule()->createSingleSchedule(
            "每天14点发送的定时任务",
            $payload,
            array("time" => $time)
        );
    }

    /**
     * 循环定时发送
     * @param $payload
     * @param $option
     * @return array
     */
    private function periodicalSchedule($payload, $option)
    {
        return $this->jPushClient->schedule()->createPeriodicalSchedule(
            "每天14点发送的定时任务",
            $payload,
            $option);
    }
}

