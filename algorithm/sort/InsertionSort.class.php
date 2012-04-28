
  public function InsertionSort()
  {
       for ($i = 1; $i < $this->data->count(); $i++)
        {
              $j = $i;
              $tmp = $this->data[$i];
              while (($j > 0) && (
                    strcmp($this->data[$j - 1]->GetSortKey(), 
                          $tmp->GetSortKey()) > 0)
                    )
              {
                    $this->data->offsetSet($j, $this->data[$j -
1]);
                    $j--;
              }
              $this->data->offsetSet($j, $tmp);
        }
  }