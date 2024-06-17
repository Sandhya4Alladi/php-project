<?php

use MongoDB\BSON\ObjectId;

class UserController extends Controller {

    public $layout = 'sample';

    public function actionIndex() {
        $authToken = Yii::app()->request->cookies['jwt_token'];
        echo $authToken;
        $this->render('application.views.video.player');
        Yii::app()->end();
    }

    public function actionProfile() {
        if (Yii::app()->request->getRequestType() === 'GET') {
            try {
                $user = UserHelper::getUserProfile();
                $this->render('profile', array('userId' => $user));
                Yii::app()->end();
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                throw new CHttpException(500, $e->getMessage());
            }
        }
    }

    public function actionEditprofile() {
        $userId = Yii::app()->request->getQuery('userId');
        $requestData = json_decode(file_get_contents('php://input'), true);
        try {
            $response = UserHelper::updateUserProfile($userId, $requestData);
            echo CJSON::encode($response);
            Yii::app()->end();
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, $e->getMessage());
        }
    }

    public function actionDeleteprofile() {
        $userId = Yii::app()->request->getParam('userId');
        try {
            $response = UserHelper::deleteUserProfile($userId);
            echo CJSON::encode($response);
            Yii::app()->end();
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, $e->getMessage());
        }
    }

    public function actionLike() {
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $response = UserHelper::likeVideo($data);
            Yii::app()->end(json_encode($response));
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, $e->getMessage());
        }
    }

    public function actionDislike() {
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $response = UserHelper::dislikeVideo($data);
            Yii::app()->end(json_encode($response));
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, $e->getMessage());
        }
    }

    public function actionWatch() {
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $response = UserHelper::addWatchLater($data);
            Yii::app()->end(json_encode($response));
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, $e->getMessage());
        }
    }

    public function actionTrack() {
        $videoId = Yii::app()->request->getQuery('id');
        try {
            $response = UserHelper::trackStatus($videoId);
            Yii::app()->end(json_encode($response));
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, $e->getMessage());
        }
    }

    public function actionCheck() {
        $videoId = Yii::app()->request->getQuery('id');
        try {
            $response = UserHelper::checkVideoStatus($videoId);
            Yii::app()->end(json_encode($response));
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new CHttpException(500, $e->getMessage());
        }
    }
}
?>
