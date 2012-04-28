  public function SelectionSort()
  {
        for ($i = 0; $i < $this->data->count(); $i++)
        {
              $min = $i;
              $j = 0;
               
              for ($j = $i + 1; $j < $this->data->count(); $j++)
              {
                    if (strcmp($this->data[$j]->GetSortKey(), 
                          $this->data[$min]->GetSortKey()) < 0)
                    {
                          $min = $j;
                    }
              }                 

              $tmp = $this->data[$min];
              $this->data->offsetSet($min, $this->data[$i]);
              $this->data->offsetSet($i, $tmp);
        }
  }