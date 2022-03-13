<?php
namespace App\Entity;

use Symfony\Bundle\MakerBundle\Str;

class Search {
    /**
     * @var string|null
     */
    private $search;

    public function getSearch()
    {
        $this->search;
    }

    public function setSearch(string $search)
    {
        $this->search = $search;

        return $this;
    }
    
}
