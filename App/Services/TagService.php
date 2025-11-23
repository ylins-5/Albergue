<?php

namespace App\Services;

require_once __DIR__ . '/../Repositories/TagRepository.php';
require_once __DIR__ . '/../Models/Tag.php';

use App\Repositories\TagRepository;
use App\Models\Tag;

class TagService {

    private $repository;

    public function __construct()
    {
        $this->repository = new TagRepository();
    }

    public function getAllTags()
    {
        return $this->repository->findAll();
    }

    public function getTagsById($id)
    {
        return $this->repository->findById($id);
    }

    public function createTag($data)
    {
        $tag = new Tag(
            null,
            $data['nome'] ?? null,
            $data['tipo'] ?? null
        );

        return $this->repository->create($tag);
    }

    public function updateTag($id, $data)
    {
        $tag = $this->repository->findById($id);
        if (!$tag) return null;

        $tag->nome = $data['nome'] ?? $tag->nome;
        $tag->tipo = $data['tipo'] ?? $tag->tipo;

        $this->repository->update($tag);
        return $tag;
    }

    public function deleteTag($id)
    {
        return $this->repository->delete($id);
    }

    public function getTagsByType($tipo)
    {
    return $this->repository->findByType($tipo);
    }

}
