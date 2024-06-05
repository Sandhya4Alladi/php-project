<?php

    class UserController extends Controller{

        public $layout = 'sample';

        public function actionIndex(){
       
            $authToken = Yii::app()->request->cookies['jwt_token'];
            echo $authToken;
        }

        public function actionEditprofile(){
            //update user
            $userId = Yii::app()->request->getQuery('userId');
            $id = Yii::app()->session['user_id'];
            if($userId === $id){
                try {
               
                    $user = User::model()->findByPk(new \MongoDB\BSON\ObjectId($userId));
                    if ($user !== null) {
                        $requestData = json_decode(file_get_contents('php://input'), true);
                        $username = $requestData['username'];
                        $email = $requestData['email'];

                        $user->username = $username;
                        $user->email = $email;
                        if ($user->save()) {
                            echo CJSON::encode($user);
                        } else {
                            throw new CHttpException(500, 'Failed to update user.');
                        }
                    } else {
                        throw new CHttpException(404, 'User not found.');
                    }
                } catch (Exception $e) {
                    throw new CHttpException(500, $e->getMessage());
                }
            } else {
                echo CJSON::encode(array('message' => 'You can update only your account!'));
                Yii::app()->end();
            }

        }

        public function actionDeleteprofile() {
            $userId = Yii::app()->request->getParam('userId');
            $id = Yii::app()->session['user_id'];
        
            if ($userId === $id) {
                try {
                    $user = User::model()->findByPk(new \MongoDB\BSON\ObjectId($userId));
        
                    if ($user !== null) {
                        if ($user->delete()) {
                            Yii::app()->user->logout();
                            $this->redirect(['/auth/logout']);
                        } else {
                            throw new CHttpException(500, 'Failed to delete user.');
                        }
                    } else {
                        throw new CHttpException(404, 'User not found.');
                    }
                } catch (Exception $e) {
                    throw new CHttpException(500, $e->getMessage());
                }
            } else {
                echo CJSON::encode(array('message' => 'You can delete only your account!'));
                Yii::app()->end();
            }
        }

        public function actionProfile(){
            //getUser
            if(Yii::app()->request->getRequestType() === 'GET'){
                $userId = Yii::app()->session['user_id'];
                $user = User::model()->findByAttributes(array('_id'=>new \MongoDB\BSON\ObjectId($userId)));
            }
        
            $this->render('profile', array('userId' => $user));
            Yii::app()->end();
        }

        public function actionLike(){
            //like
            $userId = Yii::app()->session['user_id']; 
            $videoId = Yii::app()->request->getQuery('userId');

                try {
                    $user = User::model()->findByPk($userId);
                    if ($user !== null) {
                        if (!in_array($videoId, $user->likedvideos)) {
                            $disliked = User::model()->findByAttributes(array('_id' => $userId, 'dislikedvideos' => $videoId));
                            if ($disliked !== null) {
                                $user->dislikedvideos = array_diff($user->dislikedvideos, array($videoId));
                                $user->save();
                                $video = Video::model()->findByPk($videoId);
                                if ($video !== null) {
                                    $video->dislikes -= 1;
                                    $video->save();
                                }
                            }
                            $user->likedVideos[] = $videoId;
                            $user->save();
                            $video = Video::model()->findByPk($videoId);
                            if ($video !== null) {
                                $video->likes += 1;
                                $video->save();
                            }
                        }
                    } else {
                        throw new Exception('User not found');
                    }
                    Yii::app()->end(json_encode("The video has been liked."));
                } catch (Exception $e) {
                    Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                }
        }

        public function actionDislike(){

            $userId = Yii::app()->session['user_id'];
            $videoId = Yii::app()->request->getQuery('userId');

            try {
                $user = User::model()->findByPk($userId);
                if ($user !== null) {
                    if ($user->likedvideos && in_array($videoId, $user->likedvideos)) {
                        $user->likedvideos = array_diff($user->likedvideos, array($videoId));
                        $user->save();
                        $video = Video::model()->findByPk($videoId);
                        if ($video !== null) {
                            $video->likes -= 1;
                            $video->save();
                        }
                    }
                    if (!in_array($videoId, $user->dislikedVideos)) {
                        $user->dislikedVideos[] = $videoId;
                        $user->save();
                        $video = Video::model()->findByPk($videoId);
                        if ($video !== null) {
                            $video->dislikes += 1;
                            $video->save();
                        }
                    }
                } else {
                    throw new Exception('User not found');
                }
                Yii::app()->end(json_encode("The video has been disliked."));
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            }

        }

        public function actionwatch(){
            //watch
         $userId = Yii::app()->session['user_id'];
         $videoId = Yii::app()->request->getQuery('userId');
         try{

            $user = User::model()->findByPk($videoId);
             if ($user !== null) {
                if (!in_array($videoId, $user->watchLater)) {
                    $user->watchLater[] = $videoId;
                    $user->save();
                }
         } else {
            throw new Exception('User not found');
            }

            }catch(Exception $e){
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            }
        }
        
        public function actionTrackstatus(){
            //tackStatus
            $userId = Yii::app()->session['user_id']; 
            $videoId = Yii::app()->request->getQuery('userId');

            try {
                $user = User::model()->findByAttributes(array('_id' => $userId, 'watchlater' => $videoId));
                if ($user !== null) {
                    Yii::app()->end(json_encode(array('watched' => 1)));
                } else {
                    Yii::app()->end(json_encode(array('watched' => 0)));
                }
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                Yii::app()->end(json_encode(array('error' => $e->getMessage())));
            }
        }

        public function actionCheckstatus(){

            $userId = Yii::app()->session['user_id'];  
            try {
                $liked = User::model()->findByAttributes(array('_id' => $userId, 'likedvideos' => $id));
                $disliked = User::model()->findByAttributes(array('_id' => $userId, 'dislikedvideos' => $id));
                
                if ($liked !== null) {
                    Yii::app()->end(json_encode(array('liked' => 1, 'disliked' => 0)));
                } elseif ($disliked !== null) {
                    Yii::app()->end(json_encode(array('liked' => 0, 'disliked' => 1)));
                } else {
                    Yii::app()->end(json_encode(array('liked' => 0, 'disliked' => 0)));
                }
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                Yii::app()->end(json_encode(array('error' => $e->getMessage())));
            }
        }

    }

?>