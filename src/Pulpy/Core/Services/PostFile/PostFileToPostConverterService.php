<?php

namespace Pulpy\Core\Services\PostFile;

use Symfony\Component\Yaml\Yaml;

use Pulpy\Core\Entity\Post,
    Pulpy\Core\Entity\PostFile,
    Pulpy\Core\Entity\AbstractPost;

class PostFileToPostConverterService {
    
    public function convertToPost(PostFile $postfile) {
        $post = new Post();
        $post = $this->merge($post, $postfile);

        return $post;
    }

    public function merge(AbstractPost $to, AbstractPost $from) {
        $to->setSlug($from->getSlug());
        $to->setTitle($from->getTitle());
        $to->setAuthor($from->getAuthor());
        $to->setWebsite($from->getWebsite());
        $to->setBio($from->getBio());
        $to->setTwitter($from->getTwitter());
        $to->setDate($from->getDate());
        $to->setStatus($from->getStatus());
        $to->setIntro($from->getIntro());
        $to->setContent($from->getContent());
        $to->setImage($from->getImage());
        $to->setComments($from->getComments());
        $to->setAbout($from->getAbout());
        $to->setMeta($from->getMeta());
        $to->setFingerprint($from->getFingerprint());
        $to->setLastmodified($from->getLastmodified());

        return $to;
    }
}