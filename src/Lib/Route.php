<?php

namespace App\PlusCourtChemin\Lib;

class Route
{
static function getShortestPath(array $path, int $start, int $target): array {

        $distance = $path[$target]["distance"];
        $currentNode = $target;
        $shortestPath = [$start];
        while ($currentNode !== $start) {
            if (!isset($path[$currentNode]) || $path[$currentNode]['pred'] === -1) {
                // No path found
                return [];
            }
            if (isset($path[$currentNode]["troncon_gid"])){
                array_unshift($shortestPath, $path[$currentNode]["troncon_gid"]);
            }
            $currentNode = $path[$currentNode]['pred'];

        }


        return ["path"=>$shortestPath,"distance"=>$distance];
    }
}