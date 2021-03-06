<?php

namespace CrossOverApp\UserBundle\Entity;
use CrossOverApp\UserBundle\ImageHelper;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="cross_over_news")
 * @ORM\Entity(repositoryClass="CrossOverApp\UserBundle\Repository\NewsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class News
{
     /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="news")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id",onDelete="CASCADE")
     */
    protected $user;
    
    /**
     * @var int
     *
     * @ORM\Column(name="news_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="news_title", type="string", length=255)
     */
    private $newsTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="new_description", type="text")
     */
    private $newDescription;

    /**
     * @var dateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var dateTime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    
    /**
     * @ORM\Column(name="news_image",type="string", length=255)
     */
    protected $newimage;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @Assert\File(maxSize="6000000")
     * @Assert\NotBlank(groups={"news_create"})
     */
    public $file;

    /**
     * Generated PDF document
     */
    private $pdf;
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set newsTitle
     *
     * @param string $newsTitle
     *
     * @return News
     */
    public function setNewsTitle($newsTitle)
    {
        $this->newsTitle = $newsTitle;

        return $this;
    }

    /**
     * Get newsTitle
     *
     * @return string
     */
    public function getNewsTitle()
    {
        return $this->newsTitle;
    }

    /**
     * Set newDescription
     *
     * @param string $newDescription
     *
     * @return News
     */
    public function setNewDescription($newDescription)
    {
        $this->newDescription = $newDescription;

        return $this;
    }

    /**
     * Get newDescription
     *
     * @return string
     */
    public function getNewDescription()
    {
        return $this->newDescription;
    }

    

    /**
     * Set user
     *
     * @param \CrossOverApp\UserBundle\Entity\User $user
     *
     * @return News
     */
    public function setUser(\CrossOverApp\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CrossOverApp\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return News
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return News
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return News
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    
    
    
    
    
     

    /**
     * Set newimage
     *
     * @param string $newimage
     *
     * @return News
     */
    public function setNewimage($newimage)
    {
        $this->newimage = $newimage;

        return $this;
    }

    /**
     * Get newimage
     *
     * @return string
     */
    public function getNewimage()
    {
        return $this->newimage;
    }
    
    
    //-------------------------------------------------
    //-------------- Image Upload ---------------------
    //-------------------------------------------------
    
    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }
        
       $ih=new ImageHelper('news', $this);
        $ih->upload();
    }
//---------------------------------------------------
    
  public function getAbsolutePath()
    {
        return null === $this->newimage
            ? null
            : $this->getUploadRootDir().'/'.$this->newimage;
    }
//---------------------------------------------------
    public function getWebPath()
    {
        return null === $this->newimage
            ? null
            : $this->getUploadDir().'/'.$this->newimage;
    }
//---------------------------------------------------
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
//---------------------------------------------------
    protected function getUploadDir()
    {
        return 'uploads/crossover/news';
    }

    //---------------------------------------------------
    
    /**
     * Get PDF file contents
     * @return mixed Generated PDF file contents
     */
    public function getPdf()
    {
        return $this->pdf;
    }
    
 /**
 * @ORM\PostRemove
 */
public function deleteImages()
{
     $ih=new ImageHelper('news', $this);
     $ih->deleteImages($this->newimage);
}   
}
