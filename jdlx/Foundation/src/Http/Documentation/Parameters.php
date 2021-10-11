<?php
/**
 * @OA\Parameter(
 *      parameter="pageParameter",
 *      name="page",
 *      in="query",
 *      description="Which page of items to return",
 *      required=false,
 *      @OA\Schema(
 *        type="integer"
 *      )
 *   ),
 *
 * @OA\Parameter(
 *      parameter="limitParameter10Max100",
 *      name="limit",
 *      in="query",
 *      description="Number of items to be returned per page",
 *      required=false,
 *      @OA\Schema(
 *        type="integer"
 *      )
 *   ),
 *
 * @OA\Parameter(
 *      parameter="limitParameter",
 *      name="limit",
 *      in="query",
 *      description="Number of items to be returned per page",
 *      required=false,
 *      @OA\Schema(
 *        type="integer"
 *      )
 *   ),
 * @OA\Parameter(
 *      parameter="sortParameter",
 *      name="sort",
 *      in="query",
 *      description="Sort allowed fields",
 *      required=false,
 *      @OA\Schema(
 *        type="string"
 *      )
 *   ),
 *
 *  @OA\Parameter(
 *      parameter="idParameter",
 *      name="id",
 *      in="path",
 *      description="The identifier of the entity",
 *      required=true,
 *      @OA\Schema(
 *        type="string"
 *      )
 *   ),
 *
 *  @OA\Parameter(
 *      parameter="filterParameter",
 *      name="filter",
 *      in="query",
 *      description="Filters to be applied to the query",
 *      @OA\Schema(
 *        type="object"
 *      ),
 *      required=false,
 *      explode=true,
 *      style="deepObject"
 *
 *   ),
 */
