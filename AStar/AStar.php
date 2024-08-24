<?php

namespace APP\AStar;

class AStar {

    // Public method to perform A* algorithm
    static public function findPath($start, $end, $grid) {
        $openList = [];
        $closedList = [];
        
        $startNode = new Node($start[0], $start[1]);
        $endNode = new Node($end[0], $end[1]);
        
        $openList[] = $startNode;

        while (!empty($openList)) {
            usort($openList, function($a, $b) {
                return $a->f - $b->f;
            });

            $currentNode = array_shift($openList);
            $closedList[] = $currentNode;

            if ($currentNode->x == $endNode->x && $currentNode->y == $endNode->y) {
                return self::reconstructPath($currentNode);
            }

            $children = self::generateChildren($currentNode, $grid, $endNode);

            foreach ($children as $child) {
                if (self::inList($child, $closedList)) {
                    continue;
                }

                $child->g = $currentNode->g + 1;
                $child->h = self::heuristic($child, $endNode);
                $child->f = $child->g + $child->h;

                if (self::inList($child, $openList) && $child->g > self::getNodeFromList($child, $openList)->g) {
                    continue;
                }

                $openList[] = $child;
            }
        }

        return null;
    }

    // Private method to generate children nodes
    static private function generateChildren($currentNode, $grid, $endNode) {
        $children = [];
        $moves = [
            [-1, 0], [1, 0], [0, -1], [0, 1] // Left, Right, Up, Down
        ];

        foreach ($moves as $move) {
            $nodePosition = [$currentNode->x + $move[0], $currentNode->y + $move[1]];

            if ($nodePosition[0] > count($grid) - 1 || $nodePosition[0] < 0 ||
                $nodePosition[1] > count($grid[0]) - 1 || $nodePosition[1] < 0 ||
                $grid[$nodePosition[0]][$nodePosition[1]] != 0) {
                continue;
            }

            $children[] = new Node($nodePosition[0], $nodePosition[1], $currentNode);
        }

        return $children;
    }

    // Private method to calculate heuristic (Manhattan distance)
    static private function heuristic($node, $endNode) {
        return abs($node->x - $endNode->x) + abs($node->y - $endNode->y);
    }

    // Private method to reconstruct path from end node to start
    static private function reconstructPath($currentNode) {
        $path = [];
        while ($currentNode) {
            $path[] = $currentNode;
            $currentNode = $currentNode->parent;
        }
        return array_reverse($path);
    }

    // Private method to check if a node is in a list
    static private function inList($node, $list) {
        foreach ($list as $listNode) {
            if ($node->x == $listNode->x && $node->y == $listNode->y) {
                return true;
            }
        }
        return false;
    }

    // Private method to get a node from a list
    static private function getNodeFromList($node, $list) {
        foreach ($list as $listNode) {
            if ($node->x == $listNode->x && $node->y == $listNode->y) {
                return $listNode;
            }
        }
        return null;
    }
}

class Node {
    public $x;
    public $y;
    public $parent;
    public $g;
    public $h;
    public $f;

    public function __construct($x, $y, $parent = null, $g = 0, $h = 0) {
        $this->x = $x;
        $this->y = $y;
        $this->parent = $parent;
        $this->g = $g;
        $this->h = $h;
        $this->f = $g + $h;
    }

    public function __toString() {
        return "({$this->x}, {$this->y})";
    }
}