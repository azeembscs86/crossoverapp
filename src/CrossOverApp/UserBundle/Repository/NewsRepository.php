<?php

namespace CrossOverApp\UserBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * NewsRepository
 */
class NewsRepository extends \Doctrine\ORM\EntityRepository
{
    
    public function findAllNews($user_id,$page_number = 0, $limit = 0) {
        
       $query = $this->getEntityManager()
                        ->createQuery("
            SELECT p FROM CrossOverAppUserBundle:News p
            WHERE p.user = :user_id Order by p.createdAt DESC")
                         ->setParameter('user_id', $user_id)
                         ->setFirstResult($limit * ($page_number - 1))
                         ->setMaxResults($limit);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function countAllRecord($user_id) {
        $total_record = $this->getEntityManager()
                        ->createQuery("
            SELECT p FROM CrossOverAppUserBundle:News p
            WHERE p.user = :user_id ")
                         ->setParameter('user_id', $user_id);
        try {
            return $total_record->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findlatestNewArticle($page_number = 1, $limit = 10)
    {
        $query = $this->getEntityManager()
                        ->createQuery("
            SELECT p FROM CrossOverAppUserBundle:News p
            Order by p.createdAt DESC")
                         ->setFirstResult($page_number)
                         ->setMaxResults($limit);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
