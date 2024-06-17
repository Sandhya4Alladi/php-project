<?php

use MongoDB\BSON\ObjectId;

class UserHelper {

    public static function getUserProfile() {
        $userId = Yii::app()->session['user_id'];
        return User::model()->findByAttributes(array('_id' => new ObjectId($userId)));
    }

    public static function updateUserProfile($userId, $requestData) {
        $id = Yii::app()->session['user_id'];
        if ($userId === $id) {
            try {
                $user = User::model()->findByPk(new ObjectId($userId));
                if ($user !== null) {
                    $user->username = $requestData['username'];
                    $user->email = $requestData['email'];
                    if ($user->save()) {
                        return $user;
                    } else {
                        throw new Exception('Failed to update user.');
                    }
                } else {
                    throw new Exception('User not found.');
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        } else {
            return ['message' => 'You can update only your account!'];
        }
    }

    public static function deleteUserProfile($userId) {
        $id = Yii::app()->session['user_id'];
        if ($userId === $id) {
            try {
                $user = User::model()->findByPk(new ObjectId($userId));
                if ($user !== null) {
                    if ($user->delete()) {
                        return ['status' => 200];
                    } else {
                        throw new Exception('Failed to delete user.');
                    }
                } else {
                    throw new Exception('User not found.');
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        } else {
            return ['message' => 'You can delete only your account!'];
        }
    }

    public static function likeVideo($data) {
        $userId = new ObjectId(Yii::app()->session['user_id']);
        $id = new ObjectId($data['id']);
        try {
            $user = User::model()->findByPk($userId);
            if ($user !== null) {
                if (!in_array($id, $user->likedVideos)) {
                    $disliked = User::model()->findByAttributes(array('_id' => $userId, 'dislikedVideos' => $id));
                    if ($disliked !== null) {
                        $user->dislikedVideos = array_diff($user->dislikedVideos, array($id));
                        $user->save();
                        $video = Video::model()->findByPk($id);
                        if ($video !== null) {
                            $video->dislikes -= 1;
                            $video->save();
                        }
                    }
                    $user->likedVideos[] = $id;
                    $user->save();
                    $video = Video::model()->findByPk($id);
                    if ($video !== null) {
                        $video->likes += 1;
                        $video->save();
                    }
                }
            } else {
                throw new Exception('User not found');
            }
            return "The video has been liked.";
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new Exception($e->getMessage());
        }
    }

    public static function dislikeVideo($data) {
        $userId = new ObjectId(Yii::app()->session['user_id']);
        $id = new ObjectId($data['id']);
        try {
            $user = User::model()->findByPk($userId);
            if ($user !== null) {
                if ($user->likedVideos && in_array($id, $user->likedVideos)) {
                    $user->likedVideos = array_diff($user->likedVideos, array($id));
                    $user->save();
                    $video = Video::model()->findByPk($id);
                    if ($video !== null) {
                        $video->likes -= 1;
                        $video->save();
                    }
                }
                if (!in_array($id, $user->dislikedVideos)) {
                    $user->dislikedVideos[] = $id;
                    $user->save();
                    $video = Video::model()->findByPk($id);
                    if ($video !== null) {
                        $video->dislikes += 1;
                        $video->save();
                    }
                }
            } else {
                throw new Exception('User not found');
            }
            return "The video has been disliked.";
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new Exception($e->getMessage());
        }
    }

    public static function addWatchLater($data) {
        $userId = new ObjectId(Yii::app()->session['user_id']);
        $id = new ObjectId($data['id']);
        try {
            $user = User::model()->findByPk($userId);
            if ($user !== null) {
                if (!in_array($id, $user->watchLater)) {
                    $user->watchLater[] = $id;
                    $user->save();
                }
            } else {
                throw new Exception('User not found');
            }
            return "The video has been added to watch later.";
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            throw new Exception($e->getMessage());
        }
    }

    public static function trackStatus($videoId) {
        $userId = new ObjectId(Yii::app()->session['user_id']);
        $id = new ObjectId($videoId);
        try {
            $user = User::model()->findByAttributes(array('_id' => $userId, 'watchLater' => $id));
            if ($user !== null) {
                return array('watched' => 1);
            } else {
                return array('watched' => 0);
            }
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            return array('error' => $e->getMessage());
        }
    }

    public static function checkVideoStatus($videoId) {
        $userId = new ObjectId(Yii::app()->session['user_id']);
        $id = new ObjectId($videoId);
        try {
            $liked = User::model()->findByAttributes(array('_id' => $userId, 'likedVideos' => $id));
            $disliked = User::model()->findByAttributes(array('_id' => $userId, 'dislikedVideos' => $id));
            if ($liked !== null) {
                return array('liked' => 1, 'disliked' => 0);
            } elseif ($disliked !== null) {
                return array('liked' => 0, 'disliked' => 1);
            } else {
                return array('liked' => 0, 'disliked' => 0);
            }
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            return array('error' => $e->getMessage());
        }
    }
}
?>
