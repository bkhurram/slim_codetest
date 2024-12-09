<?php

namespace App\Application\Controller;

use App\Application\Exception\HttpUnprocessableException;
use App\Application\Models\Post;
use App\Application\Models\Tag;
use App\Application\Response\PaginateResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class PostsController extends BaseController
{
	public function index(Request $request, Response $response)
	{
		$params = $request->getQueryParams();
		$query = Post::query()->with(['tags']);

		if (isset($params['tags'])) {
			$tags = $params['tags'];
			$query->whereHas('tags', function ($query) use ($tags) {
				$query->where('slug', $tags[0]);
				for($i=1; $i<count($tags); $i++) {
					$tag = $tags[$i];
					$query->orWhere('slug', $tag);
				}
			});
		}

		if (isset($params['q'])) {
			$query->where('title', 'LIKE', '%' . $params['q'] . '%');
			$query->orWhere('body', 'LIKE', '%' . $params['q'] . '%');
		}


		$page = isset($params['page']) ? (int)$params['page'] : 1;
		$perPage = isset($params['perPage']) ? (int)$params['perPage'] : 10;
		$items = $query->paginate(perPage: $perPage, page: $page);

		return $this->respondWithData($response, new PaginateResponse($items));
	}

	public function store(Request $request, Response $response)
	{
		$data = $this->getFormData($request);
		$this->validateFormData($request, $data ?? []);

		$connection = $this->capsule->getConnection();

		try{
			$connection->beginTransaction(); // Start the transaction

			$post = new Post();
			if(isset($data['id'])) {
				$post->uuid = $data['id'];
			}
			$post->fill(Arr::except($data, ['id', 'tags']));
			$post->save();

			$tags = $data['tags'];
			$tagIds = [];
			foreach ($tags as $tag) {
				$tag = Tag::firstOrCreate(['name' => $tag]);
				$tagIds[] = $tag->id;
			}

			$post->tags()->sync($tagIds);
			$post->save();

			$connection->commit(); // Commit the transaction
		} catch (\Exception $e) {
			$connection->rollBack(); // Rollback the transaction on error
			$this->logger->error("Fail create post: " . $e->getMessage());
			throw new HttpInternalServerErrorException($request);
		}


		return $this->respondWithData($response, $post);
	}

	public function view(Request $request, Response $response, array $args): Response
	{
		$uuid = $args['id'];

		$post = Post::firstWhere('uuid', $uuid);
		if (!$post) {
			throw new HttpNotFoundException($request, 'Post not found');
		}

		return $this->respondWithData($response, $post);
	}

	public function update(Request $request, Response $response, array $args)
	{
		$uuid = $args['id'];

		$post = Post::firstWhere('uuid', $uuid);
		if (!$post) {
			throw new HttpNotFoundException($request, 'Post not found');
		}

		$data = $this->getFormData($request);
		$this->validateFormData($request, $data ?? []);

		$connection = $this->capsule->getConnection();

		try{
			$connection->beginTransaction(); // Start the transaction

			$post->fill(Arr::except($data, ['id', 'tags']));
			$post->save();

			$tags = $data['tags'];
			$tagIds = [];
			foreach ($tags as $tag) {
				$tag = Tag::firstOrCreate(['name' => $tag]);
				$tagIds[] = $tag->id;
			}

			$post->tags()->sync($tagIds);
			$post->save();

			$connection->commit(); // Commit the transaction
		} catch (\Exception $e) {
			$connection->rollBack(); // Rollback the transaction on error
			$this->logger->error("Fail create post: " . $e->getMessage());
			throw new HttpInternalServerErrorException($request);
		}

		return $this->respondWithData($response, $post);
	}

	public function delete(Request $request, Response $response, array $args): Response
	{
		$uuid = $args['id'];

		$post = Post::firstWhere('uuid', $uuid);
		if (!$post) {
			throw new HttpNotFoundException($request, 'Post not found');
		}

		$post->delete();

		return $this->respondWithData(null, 204);
	}

	private function validateFormData(Request $request, array $data): void
	{
		// Define the validation rules
		$rules = [
			"id"     => ['nullable','string'], // if null generate uuid
			"title"  => ['required','string'],
			"body"   => ['required','string'],
			"status" => ['required','string', Rule::in([Post::STATUS_ONLINE, Post::STATUS_OFFLINE])],
			"tags"   => ['required',"array","min:0","max:5"],
			'tags.*' => ['required', 'string', 'distinct'], // Each item must be a unique string

		];

		$messages = [
			'title.required'  => 'Title is required.',
			'body.required'   => 'Content is required.',
			'status.required' => 'Status is required.',
			'status.in'       => 'Status is not valid.',
			'tags.required'   => 'Tags is required.',
			'tags.*.distinct' => 'Tags item in the list must be unique.',

		];

		// Validate the data
		$errors = $this->validator->validate($data, $rules, $messages);
		if($errors) {
			throw new HttpUnprocessableException($request, $errors);
		}
	}

}
