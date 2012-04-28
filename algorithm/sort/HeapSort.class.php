  public function HeapSort()
  {
        for ($i = ($this->data->count() / 2) - 1; $i >= 0; $i--)
              $this->HeapSortSiftDown($i, $this->data->count());                 

        for ($i = $this->data->count() - 1; $i >= 1; $i--)
        {
              $tmp = $this->data[0];
              $this->data->offsetSet(0, $this->data[$i]);
              $this->data->offsetSet($i, $tmp);
              $this->HeapSortSiftDown(0, $i - 1);
        }
  }     

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