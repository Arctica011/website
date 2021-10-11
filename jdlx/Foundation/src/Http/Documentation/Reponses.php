<?php
/**
 * @OA\Response(
 *      response="NotFound",
 *      description="The specified resource was not found.",
 *      @OA\JsonContent(
 *             ref="#/components/schemas/Error",
 *       )
 *
 *    ),
 * @OA\Response(
 *      response="Unauthorized",
 *      description="The users was not authorized.",
 *      @OA\JsonContent(
 *             ref="#/components/schemas/Error",
 *       )
 *
 *    )
 *
 * @OA\Response(
 *      response="Invalid",
 *      description="The provided data is invalid.",
 *      @OA\JsonContent(
 *             ref="#/components/schemas/Error",
 *       )
 *
 *    )
 *
 * @OA\Response(
 *      response="ok",
 *      description="The provided data is invalid.",
 *      @OA\JsonContent(
 *             ref="#/components/schemas/Error",
 *       )
 *
 *    )
 */
