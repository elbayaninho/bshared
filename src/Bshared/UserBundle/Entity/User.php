<?php

namespace Bshared\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="usernameCanonical", errorPath="username", message="fos_user.username.already_used", groups={"Default", "Registration", "Profile"})
 * @UniqueEntity(fields="emailCanonical", errorPath="email", message="fos_user.email.already_used", groups={"Default", "Registration", "Profile"})
 */
class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", length=6, options={"default":0})
     */
    protected $loginCount = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $firstLogin;

    /**
     * @ORM\OneToMany(targetEntity="Bshared\BsharedBundle\Entity\Article", mappedBy="author")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="Bshared\BsharedBundle\Entity\Categorie", mappedBy="author")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="Bshared\BsharedBundle\Entity\Reponse", mappedBy="author")
     */
    private $reponses;

    /**
     * @ORM\OneToMany(targetEntity="Bshared\BsharedBundle\Entity\Commentaire", mappedBy="author")
     */
    private $commentaires;

    /**
     * @ORM\OneToMany(targetEntity="Bshared\BsharedBundle\Entity\Document", mappedBy="author")
     */
    private $documents;

    public function __construct()
    {
        parent::__construct();
        $this->articles = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->reponses = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    /**
     * Set loginCount
     *
     * @param integer $loginCount
     *
     * @return User
     */
    public function setLoginCount($loginCount) {
        $this->loginCount = $loginCount;
        return $this;
    }

    /**
     * Get loginCount
     *
     * @return integer
     */
    public function getLoginCount() {
        return $this->loginCount;
    }

    /**
     * Set firstLogin
     *
     * @param \DateTime $firstLogin
     *
     * @return User
     */
    public function setFirstLogin($firstLogin) {
        $this->firstLogin = $firstLogin;
        return $this;
    }

    /**
     * Get firstLogin
     *
     * @return \DateTime
     */
    public function getFirstLogin() {
        return $this->firstLogin;
    }

    function getEnabled() {
        return $this->enabled;
    }

    function getLocked() {
        return $this->locked;
    }

    function getExpired() {
        return $this->expired;
    }

    function getExpiresAt() {
        return $this->expiresAt;
    }

    function getCredentialsExpired() {
        return $this->credentialsExpired;
    }

    function getCredentialsExpireAt() {
        return $this->credentialsExpireAt;
    }

    function setSalt($salt) {
        $this->salt = $salt;
    }

    public function setPassword($password) {
        if ($password !== null)
            $this->password = $password;
        return $this;
    }

    public function setRoles(array $roles = array()) {
        $this->roles = array();
        foreach ($roles as $role)
            $this->addRole($role);
        return $this;
    }

    /**
     * Add article
     *
     * @param \Bshared\BsharedBundle\Entity\Article $article
     *
     * @return User
     */
    public function addArticle(\Bshared\BsharedBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \Bshared\BsharedBundle\Entity\Article $article
     */
    public function removeArticle(\Bshared\BsharedBundle\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add category
     *
     * @param \Bshared\BsharedBundle\Entity\Categorie $category
     *
     * @return User
     */
    public function addCategory(\Bshared\BsharedBundle\Entity\Categorie $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Bshared\BsharedBundle\Entity\Categorie $category
     */
    public function removeCategory(\Bshared\BsharedBundle\Entity\Categorie $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add reponse
     *
     * @param \Bshared\BsharedBundle\Entity\Reponse $reponse
     *
     * @return User
     */
    public function addReponse(\Bshared\BsharedBundle\Entity\Reponse $reponse)
    {
        $this->reponses[] = $reponse;

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param \Bshared\BsharedBundle\Entity\Reponse $reponse
     */
    public function removeReponse(\Bshared\BsharedBundle\Entity\Reponse $reponse)
    {
        $this->reponses->removeElement($reponse);
    }

    /**
     * Get reponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReponses()
    {
        return $this->reponses;
    }

    /**
     * Add commentaire
     *
     * @param \Bshared\BsharedBundle\Entity\Commentaire $commentaire
     *
     * @return User
     */
    public function addCommentaire(\Bshared\BsharedBundle\Entity\Commentaire $commentaire)
    {
        $this->commentaires[] = $commentaire;

        return $this;
    }

    /**
     * Remove commentaire
     *
     * @param \Bshared\BsharedBundle\Entity\Commentaire $commentaire
     */
    public function removeCommentaire(\Bshared\BsharedBundle\Entity\Commentaire $commentaire)
    {
        $this->commentaires->removeElement($commentaire);
    }

    /**
     * Get commentaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * Add document
     *
     * @param \Bshared\BsharedBundle\Entity\Document $document
     *
     * @return User
     */
    public function addDocument(\Bshared\BsharedBundle\Entity\Document $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param \Bshared\BsharedBundle\Entity\Document $document
     */
    public function removeDocument(\Bshared\BsharedBundle\Entity\Document $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }
}
