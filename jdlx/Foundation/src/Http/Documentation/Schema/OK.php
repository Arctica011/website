<?php
/**
 * @OA\Schema(
 *  schema="OK",
 *  title="OK",
 *  description="A response equivalant to 204 but returning content",
 *  readOnly=true,
 *  @OA\Property(
 *      property="data",
 *      type="object",
 *      properties={
 *        @OA\Property(
 *          property="result",
 *          description="Whether an action was taken",
 *          type="bool"
 *        ),
 *
 *        @OA\Property(
 *          property="message",
 *          description="A human readable version of the result of the action",
 *          type="string"
 *        )
 *     }
 *  )
 * )
 */
