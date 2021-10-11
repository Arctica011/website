<?php
/**
 * @OA\Schema(
 *  schema="Error",
 *  title="Error",
 *  description="A formatted error",
 *  readOnly=true,
 *  @OA\Property(
 *      property="error",
 *      type="object",
 *      properties={
 *        @OA\Property(
 *          property="code",
 *          description="This property value will usually represent the HTTP response code.",
 *          type="integer"
 *        ),
 *
 *        @OA\Property(
 *          property="message",
 *          description="A human readable message providing more details about the error. If there are multiple errors, message will be the message for the first error.",
 *          type="string"
 *        ),
 *
 *        @OA\Property(
 *          property="errors",
 *          description="Container for any additional information regarding the error. ",
 *           type="array",
 *           @OA\items(
 *              type="object",
 *           )
 *        )
 *     }
 *  )
 * )
 */
