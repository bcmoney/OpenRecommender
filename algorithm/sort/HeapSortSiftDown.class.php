  private function HeapSortSiftDown($i, $arraySize)
  {
        $done = 0;
        while (($i * 2 <= $arraySize) && (!$done))
        {
              if ($i * 2 == $arraySize)
                   $maxChild = $i * 2;
              elseif (strcmp($this->data[$i * 2]->GetSortKey(),
                    $this->data[$i * 2 + 1]->GetSortKey()) > 0)
                    $maxChild = $i * 2;
              else
                    $maxChild = $i * 2 +
1;                       

              if (strcmp($this->data[$i]->GetSortKey(),
                    $this->data[$maxChild]) < 0)
              {
                    $tmp = $this->data[$i];
                    $this->data->offsetSet($i, $this->data
[$maxChild]);
                    $this->data->offsetSet($maxChild, $temp);
                    $i = $maxChild;
              }
              else
                    $done = 1;
        }
  }