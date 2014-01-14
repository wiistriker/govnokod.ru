<?php
namespace Govnokod\CommentBundle\Twig;

use Govnokod\UserBundle\Entity\User;
use Twig_Extension;
use Twig_Function_Method;

class CommentExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'user_avatar_url' => new Twig_Function_Method($this, 'userAvatarUrl'),
            'ul_tree' => new Twig_Function_Method($this, 'ULTree', array(
                'is_safe' => array('html')
            )),
        );
    }

    public function userAvatarUrl(User $user = null, $width = 20)
    {
        $avatar_url = null;

        if ($user) {
            $user_avatar_string = ''; //$user->getAvatarString();

            if ($user_avatar_string) {
                if ($user_avatar_string == 'gravatar') {
                    if ($user->getEmail()) {
                        $gravatar_hash = md5(strtolower(trim($user->getEmail())));
                        $avatar_url = 'http://www.gravatar.com/avatar/' . $gravatar_hash . '?s=' . $width;
                    }
                }
            }
        }

        if (!$avatar_url) {
            $avatar_url = '/images/avatars/noavatar_' . $width . '.png';
        }

        return $avatar_url;
    }

    public function ULTree($tree_level, $last_tree_level)
    {
        if ($tree_level < $last_tree_level):
            $level_down = $last_tree_level - $tree_level;

            return str_repeat('</li></ul>', $level_down) . '</li>';
        elseif ($tree_level == $last_tree_level):
            return '</li>';
        else:
            return '<ul>';
        endif;
    }

    public function getName()
    {
        return 'govnokod_comment';
    }
}
